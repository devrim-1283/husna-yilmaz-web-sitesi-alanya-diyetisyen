<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Yetkisiz erişim!'
    ]);
    exit;
}

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek metodu'
    ]);
    exit;
}

// AJAX request kontrolü
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'AJAX isteği gerekli'
    ]);
    exit;
}

// ID al
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz mesaj ID'
    ]);
    exit;
}

try {
    // Mesajı okundu olarak işaretle
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Mesaj okundu olarak işaretlendi'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'İşlem başarısız oldu'
        ]);
    }
} catch (PDOException $e) {
    error_log("Mark read error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu'
    ]);
}
?>

