<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Rate Limiting: 1 session 1 saatte 5 istek
function checkRateLimit() {
    if (!isset($_SESSION['form_requests'])) {
        $_SESSION['form_requests'] = [];
    }
    
    $current_time = time();
    $one_hour_ago = $current_time - 3600; // 1 saat = 3600 saniye
    
    // Eski istekleri temizle
    $_SESSION['form_requests'] = array_filter($_SESSION['form_requests'], function($timestamp) use ($one_hour_ago) {
        return $timestamp > $one_hour_ago;
    });
    
    // Son 1 saatte 5 veya daha fazla istek var mı?
    if (count($_SESSION['form_requests']) >= 5) {
        return false;
    }
    
    // Yeni isteği kaydet
    $_SESSION['form_requests'][] = $current_time;
    return true;
}

// Rate limit kontrolü
if (!checkRateLimit()) {
    echo json_encode([
        'success' => false,
        'message' => 'Çok fazla istek gönderdiniz. Lütfen 1 saat sonra tekrar deneyin.'
    ]);
    exit;
}

// Debug: Log request details
error_log("=== APPOINTMENT REQUEST DEBUG ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Headers: " . json_encode(getallheaders()));
error_log("POST Data: " . json_encode($_POST));
error_log("Request URI: " . $_SERVER['REQUEST_URI']);

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek metodu. Lütfen sayfayı yenileyip tekrar deneyin.',
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'expected' => 'POST',
            'uri' => $_SERVER['REQUEST_URI']
        ]
    ]);
    exit;
}

// AJAX request kontrolü (esnek)
$isAjax = (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (isset($_POST['ajax']) && $_POST['ajax'] === '1')
);

// Form verilerini al ve temizle
$name = sanitizeInput($_POST['name'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$appointment_date = sanitizeInput($_POST['appointment_date'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Tarih kontrolü - Geçmiş tarih seçilemesin
if (!empty($appointment_date)) {
    $selected_datetime = strtotime($appointment_date);
    $current_datetime = time();
    
    if ($selected_datetime < $current_datetime) {
        echo json_encode([
            'success' => false,
            'message' => 'Geçmiş tarih/saat seçemezsiniz! Lütfen gelecek bir tarih seçin.'
        ]);
        exit;
    }
}

// Mesaj karakter sınırı kontrolü (200 karakter)
if (strlen($message) > 200) {
    echo json_encode([
        'success' => false,
        'message' => 'Mesajınız çok uzun! Maksimum 200 karakter olmalıdır. (Şu an: ' . strlen($message) . ' karakter)'
    ]);
    exit;
}

// Telefon numarası doğrulama (esnek - tüm formatlar kabul edilir)
function validatePhone($phone) {
    // Sadece rakamları al
    $phone_digits = preg_replace('/[^0-9]/', '', $phone);
    
    // En az 10 haneli olmalı (0530... veya +90530... veya 90530... gibi)
    if (strlen($phone_digits) >= 10) {
        return $phone_digits;
    }
    
    return false;
}

// Zorunlu alanlar kontrolü
if (empty($name) || empty($phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Ad Soyad ve Telefon alanları zorunludur!'
    ]);
    exit;
}

// Telefon doğrulama
$validated_phone = validatePhone($phone);
if (!$validated_phone) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz telefon numarası! En az 10 haneli bir numara giriniz. (Örn: 05301234567, +90 530 123 45 67)'
    ]);
    exit;
}

// Email doğrulama (varsa)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz email adresi!'
    ]);
    exit;
}

try {
    // Randevu mesajını oluştur
    $full_message = "🗓️ RANDEVU TALEBİ\n\n";
    $full_message .= "Tarih: " . ($appointment_date ? date('d.m.Y H:i', strtotime($appointment_date)) : 'Belirtilmedi') . "\n";
    $full_message .= "Mesaj: " . ($message ?: 'Mesaj yok') . "\n";
    
    // Veritabanına kaydet (type = 'appointment')
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, phone, message, type, created_at, is_read) 
        VALUES (?, ?, ?, ?, 'appointment', NOW(), 0)
    ");
    
    $stmt->execute([
        $name,
        $email ?: null,
        $validated_phone,
        $full_message
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Randevu talebiniz başarıyla alındı! En kısa sürede size dönüş yapılacaktır.'
    ]);
    
} catch (PDOException $e) {
    error_log("Randevu kayıt hatası: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.'
    ]);
}
?>

