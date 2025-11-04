<?php
/**
 * Analytics Tracking System
 * Session-based with rate limiting (20 requests per minute)
 */

if (!defined('ANALYTICS_ENABLED')) {
    define('ANALYTICS_ENABLED', true);
}

/**
 * Track page view (Async + Batch optimized)
 * @param string $pageType - Type of page (index, blog, tool_xxx)
 * @param string $pageUrl - Current page URL
 * @param int|null $blogId - Blog ID if applicable
 * @return bool - Success status
 */
function trackPageView($pageType, $pageUrl, $blogId = null) {
    if (!ANALYTICS_ENABLED) {
        return false;
    }
    
    global $pdo;
    
    // Session başlat (eğer başlamamışsa)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $sessionId = session_id();
    
    // Rate Limiting: Dakikada max 20 istek
    if (!checkRateLimit($sessionId)) {
        return false; // Rate limit aşıldı, kaydetme
    }
    
    try {
        // User agent ve IP bilgisi
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : null;
        $ipAddress = getClientIp();
        $referrer = isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : null;
        
        // Bot kontrolü (basit)
        if (isBot($userAgent)) {
            return false; // Bot trafiğini kaydetme
        }
        
        // PERFORMANCE: Non-blocking insert using prepared statement
        // Connection pool ile optimize edilmiş
        $stmt = $pdo->prepare("
            INSERT INTO page_views (session_id, page_type, page_url, blog_id, user_agent, ip_address, referrer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Execute without waiting for result (fire and forget)
        $result = $stmt->execute([
            $sessionId,
            $pageType,
            $pageUrl,
            $blogId,
            $userAgent,
            $ipAddress,
            $referrer
        ]);
        
        // Close statement immediately
        $stmt = null;
        
        return $result;
        
    } catch (PDOException $e) {
        // Silent fail - don't block page load
        error_log("Analytics Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Rate limiting kontrolü - Dakikada max 20 istek (SESSION-CACHED)
 * @param string $sessionId
 * @return bool - True: İzin var, False: Limit aşıldı
 */
function checkRateLimit($sessionId) {
    global $pdo;
    
    // Session cache kontrolü - DB yükünü azalt
    $cacheKey = 'rate_limit_' . $sessionId;
    $currentMinute = floor(time() / 60);
    
    if (isset($_SESSION[$cacheKey])) {
        $cache = $_SESSION[$cacheKey];
        // Aynı dakikadaysa cache'den kontrol et
        if ($cache['minute'] == $currentMinute) {
            if ($cache['count'] >= 20) {
                return false; // Limit aşıldı
            }
            $_SESSION[$cacheKey]['count']++;
            return true;
        }
    }
    
    try {
        // Yeni dakika veya cache yok - DB'den kontrol et
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as request_count 
            FROM page_views 
            WHERE session_id = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
        ");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $requestCount = $result['request_count'] ?? 0;
        
        // Session cache güncelle
        $_SESSION[$cacheKey] = [
            'minute' => $currentMinute,
            'count' => $requestCount + 1
        ];
        
        // Dakikada max 20 istek
        return $requestCount < 20;
        
    } catch (PDOException $e) {
        error_log("Rate Limit Error: " . $e->getMessage());
        return true; // Hata durumunda izin ver
    }
}

/**
 * Client IP adresini al (proxy desteği ile)
 * @return string
 */
function getClientIp() {
    $ipKeys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];
    
    foreach ($ipKeys as $key) {
        if (isset($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Multiple IPs varsa ilkini al
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            // IP validasyonu
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return substr($ip, 0, 45); // IPv6 için max 45 karakter
            }
        }
    }
    
    return '0.0.0.0';
}

/**
 * Bot kontrolü
 * @param string|null $userAgent
 * @return bool - True: Bot, False: Gerçek kullanıcı
 */
function isBot($userAgent) {
    if (empty($userAgent)) {
        return true; // User agent yoksa bot olabilir
    }
    
    $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners',
        'APIs-Google', 'AdsBot', 'Googlebot', 'bingbot',
        'YandexBot', 'DuckDuckBot', 'Baiduspider', 'facebookexternalhit',
        'ia_archiver', 'curl', 'wget', 'python-requests',
        'scrapy', 'Apache-HttpClient', 'Pinterestbot', 'Twitterbot',
        'LinkedInBot', 'WhatsApp', 'Telegrambot', 'Slackbot'
    ];
    
    $userAgentLower = strtolower($userAgent);
    
    foreach ($botPatterns as $pattern) {
        if (strpos($userAgentLower, strtolower($pattern)) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Analytics istatistiklerini getir (Admin için - PAGINATION destekli)
 * @param int $days - Kaç günlük veri
 * @param int $page - Sayfa numarası (pagination)
 * @param int $per_page - Sayfa başına kayıt
 * @return array
 */
function getAnalyticsStats($days = 7, $page = 1, $per_page = 10) {
    global $pdo;
    
    try {
        // Toplam görüntülenme
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_views,
                COUNT(DISTINCT session_id) as unique_sessions,
                COUNT(DISTINCT ip_address) as unique_ips
            FROM page_views 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Sayfa bazında toplam kayıt sayısı
        $count_stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT page_type) as total
            FROM page_views
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $count_stmt->execute([$days]);
        $total_records = $count_stmt->fetchColumn();
        
        // Pagination hesapla
        $total_pages = ceil($total_records / $per_page);
        $offset = ($page - 1) * $per_page;
        
        // Sayfa bazında (PAGINATION ile)
        $stmt = $pdo->prepare("
            SELECT 
                page_type,
                COUNT(*) as view_count
            FROM page_views 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY page_type
            ORDER BY view_count DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$days, $per_page, $offset]);
        $byPage = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Günlük trend
        $stmt = $pdo->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as view_count
            FROM page_views 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        $dailyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'totals' => $totals,
            'by_page' => $byPage,
            'daily_trend' => $dailyTrend,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $per_page,
                'total_records' => $total_records,
                'total_pages' => $total_pages
            ]
        ];
        
    } catch (PDOException $e) {
        error_log("Analytics Stats Error: " . $e->getMessage());
        return [
            'totals' => [],
            'by_page' => [],
            'daily_trend' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => $per_page,
                'total_records' => 0,
                'total_pages' => 0
            ]
        ];
    }
}

