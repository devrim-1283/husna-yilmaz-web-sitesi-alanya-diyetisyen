<?php
/**
 * PHP Router for Clean URLs
 * Works with Nginx and other web servers
 */

// Config'i yükle (eğer yüklenmemişse)
if (!defined('SITE_URL')) {
    require_once __DIR__ . '/config/config.php';
}

// Request URI'yi al
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$requestPath = rtrim($requestPath, '/');

// Query string'i al
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Admin panel ve process endpoint'leri direkt geç
if (strpos($requestPath, '/hsnpanel2024secure/') === 0 ||
    strpos($requestPath, '/process_appointment') === 0 ||
    strpos($requestPath, '/generate_sitemap') === 0 ||
    strpos($requestPath, '/reset_session') === 0) {
    return false; // Let server handle it
}

// Root path - return false to let index.php handle it
if ($requestPath === '' || $requestPath === '/') {
    return false; // Let index.php handle root
}

// Blog detail - /blog/{id}/{slug}
if (preg_match('#^/blog/(\d+)/([a-zA-Z0-9-]+)/?$#', $requestPath, $matches)) {
    $_GET['id'] = $matches[1];
    require_once __DIR__ . '/blog-detail.php';
    return true;
}

// Success story detail - /success-story/{id}/{slug}
if (preg_match('#^/success-story/(\d+)/([a-zA-Z0-9-]+)/?$#', $requestPath, $matches)) {
    $_GET['id'] = $matches[1];
    require_once __DIR__ . '/success-story-detail.php';
    return true;
}

// Clean URL - remove leading slash and try .php file
$phpFile = ltrim($requestPath, '/');
$filePath = __DIR__ . '/' . $phpFile . '.php';

// Check if PHP file exists
if (file_exists($filePath)) {
    require_once $filePath;
    return true;
}

// If not found, return false to let server handle 404
return false;

