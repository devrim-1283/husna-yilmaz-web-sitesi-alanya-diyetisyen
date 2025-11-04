<?php
// Veritabanı bağlantısı - Coolify uyumlu

// .env dosyasını yükle (basit parser) - Coolify'de environment variable'lar kullanılır
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Helper function: Environment variable'ı getir (Coolify uyumlu)
function getEnvVar($key, $default = null) {
    // Önce getenv() dene (Coolify'de bu kullanılır)
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    // Sonra $_ENV'den dene
    return $_ENV[$key] ?? $default;
}

// Veritabanı ayarları - Coolify environment variable'larından al
define('DB_HOST', getEnvVar('DB_HOST', 'localhost'));
define('DB_PORT', getEnvVar('DB_PORT', '3306'));
define('DB_NAME', getEnvVar('DB_NAME', 'husnayilmaz_db'));
define('DB_USER', getEnvVar('DB_USER', 'root'));
define('DB_PASS', getEnvVar('DB_PASS', ''));

// PDO bağlantısı - Performans için optimize edildi (yüksek trafik için)
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            // Performans optimizasyonları (yüksek trafik için)
            PDO::ATTR_PERSISTENT => false, // Connection pooling için false
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]
    );
} catch (PDOException $e) {
    // Hata mesajını logla
    error_log("Database Connection Error: " . $e->getMessage());
    error_log("DSN: " . $dsn);
    error_log("User: " . DB_USER);
    
    // Debug mode açıksa detaylı hata göster
    $debug_mode = getEnvVar('DEBUG_MODE', 'false');
    if ($debug_mode === 'true' || $debug_mode === true) {
        die("Veritabanı bağlantısı başarısız: " . $e->getMessage() . "<br>DSN: " . $dsn);
    } else {
        die("Veritabanı bağlantısı başarısız. Lütfen yönetici ile iletişime geçin.");
    }
}

