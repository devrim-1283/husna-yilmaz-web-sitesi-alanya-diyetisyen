<?php
/**
 * Session Sıfırlama (Test Amaçlı)
 * Bu dosya sadece geliştirme sırasında kullanılmalıdır.
 */

session_start();
session_destroy();
session_start();

echo json_encode([
    'success' => true,
    'message' => 'Session başarıyla sıfırlandı! Artık yeni randevu oluşturabilirsiniz.',
    'timestamp' => date('Y-m-d H:i:s')
]);

