<?php
// Genel site ayarları - Coolify uyumlu

// Helper function: Environment variable'ı getir (Coolify uyumlu)
if (!function_exists('getEnvVar')) {
    function getEnvVar($key, $default = null) {
        // Önce getenv() dene (Coolify'de bu kullanılır)
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        // Sonra $_ENV'den dene
        return $_ENV[$key] ?? $default;
    }
}

// Environment mode (production/development)
$app_env = getEnvVar('APP_ENV', 'production');

// Hata raporlama (production'da kapalı)
if ($app_env === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Session ayarları - Coolify için optimize edildi
session_start([
    'cookie_httponly' => 1,
    'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 1 : 0,
    'cookie_samesite' => 'Lax'
]);

// Site sabitleri - Coolify environment variable'larından al
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
define('SITE_URL', getEnvVar('SITE_URL', $protocol . $host));
define('CONTACT_EMAIL', getEnvVar('CONTACT_EMAIL', 'destek@husnayilmaz.com'));
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// Uploads klasörünü otomatik oluştur (Coolify deploy sonrası için)
$uploadDirs = [
    __DIR__ . '/../assets/uploads/',
    __DIR__ . '/../assets/uploads/blogs/',
    __DIR__ . '/../assets/uploads/certificates/',
    __DIR__ . '/../assets/uploads/services/',
    __DIR__ . '/../assets/uploads/stories/'
];

foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/database.php';

// Yardımcı fonksiyonları dahil et
require_once __DIR__ . '/../includes/functions.php';

// Ayarları veritabanından yükle
$settings = [];
try {
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    // Sessizce devam et
}

// CSRF Token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

