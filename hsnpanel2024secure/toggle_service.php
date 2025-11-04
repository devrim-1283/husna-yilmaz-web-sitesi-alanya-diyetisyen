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

// ID ve status al
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? intval($_POST['status']) : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz hizmet ID'
    ]);
    exit;
}

try {
    // Durumu güncelle
    $stmt = $pdo->prepare("UPDATE services SET active = ? WHERE id = ?");
    
    if ($stmt->execute([$status, $id])) {
        echo json_encode([
            'success' => true,
            'status' => $status,
            'message' => 'Durum başarıyla güncellendi'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'İşlem başarısız oldu'
        ]);
    }
} catch (PDOException $e) {
    error_log("Toggle service error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu'
    ]);
}
?>

