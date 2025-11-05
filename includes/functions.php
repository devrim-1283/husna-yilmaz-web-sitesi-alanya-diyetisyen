<?php
/**
 * Yardımcı Fonksiyonlar
 */

/**
 * XSS koruması için çıktıyı temizle
 */
function clean($string) {
    if ($string === null || $string === '') {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * SQL Injection koruması - Gelişmiş güvenlik
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
        return $input;
    }
    
    // Trim whitespace
    $input = trim($input);
    
    // Remove null bytes
    $input = str_replace(chr(0), '', $input);
    
    // Remove SQL keywords and dangerous patterns
    $dangerous_patterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bUPDATE\b.*\bSET\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(SLEEP\(|BENCHMARK\()/i',
        '/(<script|javascript:|onclick|onerror)/i'
    ];
    
    foreach ($dangerous_patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            // Log the attempt (optional)
            error_log("SQL Injection attempt detected: " . $input);
            // Return empty string for dangerous input
            return '';
        }
    }
    
    // HTML entities for extra safety
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * Database güvenli veri temizleme
 */
function escapeData($conn, $data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = escapeData($conn, $value);
        }
        return $data;
    }
    
    // mysqli_real_escape_string kullan
    return mysqli_real_escape_string($conn, sanitizeInput($data));
}

/**
 * URL'e güvenli slug oluştur
 */
function createSlug($text) {
    $turkish = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
    $english = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
    $text = str_replace($turkish, $english, $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Okuma süresi hesapla (dakika)
 */
function calculateReadingTime($content) {
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200);
    return max(1, $minutes);
}

/**
 * Tarih formatla
 */
function formatDate($date) {
    $months = [
        'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart',
        'April' => 'Nisan', 'May' => 'Mayıs', 'June' => 'Haziran',
        'July' => 'Temmuz', 'August' => 'Ağustos', 'September' => 'Eylül',
        'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
    ];
    $formatted = date('d F Y', strtotime($date));
    return str_replace(array_keys($months), array_values($months), $formatted);
}

/**
 * Metin kısalt
 */
function truncate($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * Dosya upload
 */
function uploadFile($file, $path = 'uploads') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return false;
    }
    
    $newname = uniqid() . '.' . $ext;
    $destination = __DIR__ . '/../assets/' . $path . '/' . $newname;
    
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $path . '/' . $newname;
    }
    
    return false;
}

/**
 * Dosya sil
 */
function deleteFile($filepath) {
    $fullpath = __DIR__ . '/../assets/' . $filepath;
    if (file_exists($fullpath)) {
        return unlink($fullpath);
    }
    return false;
}

/**
 * CSRF token doğrula
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Admin kontrolü
 */
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Admin girişi zorunlu
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /hsnpanel2024secure/login.php');
        exit;
    }
}

/**
 * Redirect
 */
function redirect($url, $permanent = false) {
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit;
}

/**
 * Pagination hesapla
 */
function paginate($total, $per_page, $current_page) {
    $total_pages = ceil($total / $per_page);
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset
    ];
}

/**
 * Meta tag oluştur
 */
function generateMetaTags($title = '', $description = '', $keywords = '', $image = '') {
    global $settings;
    
    $site_title = $settings['site_title'] ?? 'Diyetisyen Hüsna Yılmaz';
    $full_title = $title ? $title . ' - ' . $site_title : $site_title . ' - Alanya Sağlıklı Beslenme Uzmanı';
    $desc = $description ?: ($settings['site_description'] ?? 'Alanya\'da profesyonel diyet ve beslenme danışmanlığı.');
    $keys = $keywords ?: ($settings['site_keywords'] ?? 'Alanya diyetisyen, Alanya sağlıklı beslenme');
    $img = $image ? SITE_URL . '/' . $image : SITE_URL . '/assets/img/hh.jpg';
    $url = SITE_URL . $_SERVER['REQUEST_URI'];
    
    return [
        'title' => $full_title,
        'description' => $desc,
        'keywords' => $keys,
        'image' => $img,
        'url' => $url
    ];
}

/**
 * URL-friendly slug oluştur
 */
function slugify($text) {
    // Türkçe karakterleri değiştir
    $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','ç','Ç');
    $en = array('s','s','i','i','g','g','u','u','o','o','c','c');
    $text = str_replace($tr, $en, $text);
    
    // Küçük harfe çevir
    $text = strtolower($text);
    
    // Alfanumerik olmayan karakterleri tire ile değiştir
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Baştaki ve sondaki tireleri kaldır
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Cache-busting ile resim/CSS/JS URL'i oluştur
 * Deploy sonrası cache sorununu önlemek için filemtime + hash + file size kullanır
 * Yeni upload edilen resimler için cache'i bypass eder
 * 
 * @param string $path Asset dosya yolu (örn: 'assets/img/image.jpg' veya '/assets/css/style.css')
 * @return string Cache-busting parametresi ile URL
 */
function imageUrl($path) {
    // Path'i normalize et (başındaki / varsa kaldır)
    $path = ltrim($path, '/');
    
    // Dosya yolu oluştur
    $filePath = __DIR__ . '/../' . $path;
    
    // Dosya varsa filemtime, hash ve file size kullan (daha güçlü cache-busting)
    if (file_exists($filePath)) {
        $mtime = filemtime($filePath); // Dosya değişiklik zamanı
        $hash = substr(md5_file($filePath), 0, 12); // İlk 12 karakter hash (daha güçlü)
        $size = filesize($filePath); // Dosya boyutu (yeni upload için)
        // Timestamp + hash + size kombinasyonu ile maksimum cache-busting
        return '/' . $path . '?v=' . $mtime . '-' . $hash . '-' . $size;
    }
    
    // Dosya yoksa normal URL döndür (cache-busting olmadan)
    return '/' . $path;
}

/**
 * Asset URL'i oluştur (imageUrl için alias)
 * 
 * @param string $path Asset dosya yolu
 * @return string Cache-busting parametresi ile URL
 */
function assetUrl($path) {
    return imageUrl($path);
}

