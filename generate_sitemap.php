<?php
/**
 * Sitemap Generator
 * Blog eklendiğinde/güncellendiğinde otomatik güncellenir
 */

// Veritabanı bağlantısını kontrol et ve gerekirse yükle
if (!isset($pdo) || $pdo === null) {
    // Önce config'i dene
    if (file_exists(__DIR__ . '/config/config.php')) {
        require_once __DIR__ . '/config/config.php';
    } elseif (file_exists(__DIR__ . '/config/database.php')) {
        // Config yoksa direkt database.php'yi yükle
        require_once __DIR__ . '/config/database.php';
    } else {
        die('Database configuration not found!');
    }
}

// $pdo'nun hala tanımlı olmadığından emin ol
if (!isset($pdo) || $pdo === null) {
    die('Database connection failed!');
}

// Site bilgileri
$siteUrl = $_ENV['SITE_URL'] ?? 'https://husnayilmaz.devrimtuncer.com';
$siteUrl = rtrim($siteUrl, '/');

// Sitemap XML başlat
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

// Statik sayfalar
$staticPages = [
    ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => '/about', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/success-stories', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => '/blog', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
    
    // Araçlar
    ['loc' => '/vki', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/bmh', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/bel-kalca-orani', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-kalori', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-karbonhidrat', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-makro', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-protein', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-su', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/gunluk-yag', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/ideal-kilo', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/vucut-yag-orani', 'priority' => '0.7', 'changefreq' => 'monthly'],
];

foreach ($staticPages as $page) {
    $url = $xml->addChild('url');
    $url->addChild('loc', $siteUrl . $page['loc']);
    $url->addChild('changefreq', $page['changefreq']);
    $url->addChild('priority', $page['priority']);
    $url->addChild('lastmod', date('Y-m-d'));
}

// Blog yazıları
$blogs = [];
try {
    $stmt = $pdo->query("SELECT slug, created_at, updated_at FROM blogs WHERE active = 1 ORDER BY created_at DESC");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($blogs as $blog) {
        $url = $xml->addChild('url');
        $url->addChild('loc', $siteUrl . '/blog/' . urlencode($blog['slug']));
        $url->addChild('changefreq', 'monthly');
        $url->addChild('priority', '0.8');
        
        // Son güncelleme tarihi veya oluşturma tarihi
        $lastmod = $blog['updated_at'] ?? $blog['created_at'];
        $url->addChild('lastmod', date('Y-m-d', strtotime($lastmod)));
    }
    
} catch (PDOException $e) {
    error_log("Sitemap Blog Error: " . $e->getMessage());
    $blogs = []; // Hata durumunda boş array
}

// XML'i güzel formatta çıkar
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());

// Dosyaya yaz
$sitemapPath = __DIR__ . '/sitemap.xml';
file_put_contents($sitemapPath, $dom->saveXML());

// Sadece CLI modunda veya doğrudan çağrıldığında çıktı ver
// Admin panelden include edilirse sessiz çalış (header hatası önlenir)
$isCLI = php_sapi_name() === 'cli';
$calledDirectly = !isset($GLOBALS['__sitemap_included__']);

if ($isCLI || $calledDirectly) {
    // Çalıştırılma bilgisi
    echo "✅ Sitemap başarıyla oluşturuldu!\n";
    echo "📍 Konum: " . $sitemapPath . "\n";
    echo "📊 Toplam URL: " . (count($staticPages) + count($blogs)) . "\n";
    echo "🕐 Tarih: " . date('Y-m-d H:i:s') . "\n";
    
    // Eğer komut satırından değil de web'den çağrılıyorsa
    if (!$isCLI) {
        header('Content-Type: text/plain; charset=UTF-8');
    }
}

