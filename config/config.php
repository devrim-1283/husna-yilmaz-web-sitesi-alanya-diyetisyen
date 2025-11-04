<?php
// Genel site ayarları - Coolify uyumlu

// Veritabanı bağlantısını dahil et (getEnvVar fonksiyonu burada tanımlı)
require_once __DIR__ . '/database.php';

// Environment mode (production/development)
$app_env = getEnvVar('APP_ENV', 'production');

// Hata raporlama - Debug mode kontrolü
$debug_mode = getEnvVar('DEBUG_MODE', 'false');
$debug_mode = ($debug_mode === 'true' || $debug_mode === true);

if ($app_env === 'development' || $debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL); // Hataları görmek için açık bırak (log'a yazılır)
    ini_set('display_errors', 0); // Ekranda gösterme
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
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
        // Permission kontrolü - hata olsa bile devam et
        @mkdir($dir, 0755, true);
        // Hata logla ama siteyi durdurma
        if (!is_dir($dir)) {
            error_log("Warning: Could not create upload directory: " . $dir);
        }
    }
}

// Yardımcı fonksiyonları dahil et
require_once __DIR__ . '/../includes/functions.php';

// Ayarları veritabanından yükle
$settings = [];
try {
    // Önce settings tablosunun var olup olmadığını kontrol et
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings LIMIT 1");
    // Tablo varsa tüm ayarları yükle
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    // Tablo yoksa veya hata varsa sessizce devam et (ilk kurulum olabilir)
    error_log("Settings table error (may not exist yet): " . $e->getMessage());
    // Varsayılan ayarları kullan
    $settings = [
        'whatsapp_number' => getEnvVar('WHATSAPP_NUMBER', '905536998982'),
        'instagram_url' => getEnvVar('INSTAGRAM_URL', 'https://www.instagram.com/devrimsoft/'),
        'site_title' => 'Diyetisyen Hüsna Yılmaz',
        'site_description' => 'Alanya\'da profesyonel diyet ve beslenme danışmanlığı.',
        'working_hours' => getEnvVar('WORKING_HOURS', 'Pazartesi - Cuma: 09:00 - 17:30 | Cumartesi: 09:00 - 14:00 | Pazar: Kapalı')
    ];
}

// CSRF Token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

