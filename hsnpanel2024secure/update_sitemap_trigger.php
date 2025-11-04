<?php
/**
 * Sitemap Update Trigger
 * Blog ekleme/güncelleme/silme işlemlerinden sonra sitemap'i otomatik günceller
 */

function updateSitemap() {
    $generateScript = dirname(__DIR__) . '/generate_sitemap.php';
    
    if (file_exists($generateScript)) {
        // PHP CLI kullan
        if (function_exists('exec')) {
            $output = [];
            $returnCode = 0;
            exec('php ' . escapeshellarg($generateScript) . ' 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                error_log("Sitemap updated successfully");
                return true;
            } else {
                error_log("Sitemap update failed: " . implode("\n", $output));
            }
        } else {
            // Eğer exec kapalıysa, include ile çalıştır
            try {
                // Global scope'tan $pdo'yu çağır
                global $pdo;
                
                // Bayrak set et: generate_sitemap.php'ye include edildiğini bildir
                $GLOBALS['__sitemap_included__'] = true;
                
                include $generateScript;
                
                // Bayrağı temizle
                unset($GLOBALS['__sitemap_included__']);
                
                return true;
            } catch (Exception $e) {
                error_log("Sitemap update error: " . $e->getMessage());
            }
        }
    }
    
    return false;
}

