<?php
/**
 * Assets Backup Script
 * assets/img ve assets/uploads klasörlerini zip olarak export eder
 */

require_once __DIR__ . '/../config/config.php';
requireAdmin();

// CSRF token kontrolü
if (!isset($_GET['token']) || !verifyCsrfToken($_GET['token'])) {
    http_response_code(403);
    die('Unauthorized');
}

// Zip dosyası oluşturma için ZipArchive kontrolü
if (!class_exists('ZipArchive')) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'ZipArchive sınıfı bulunamadı. PHP zip extension kurulu olmalı.'
    ]);
    exit;
}

// Tarih formatı
$date = date('Y-m-d_H-i-s');
$filename = "assets_backup_{$date}.zip";

// Headers
header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Cache-Control: no-cache, must-revalidate');
header('X-Accel-Buffering: no');

try {
    // Assets klasör yolları
    $assetsPath = __DIR__ . '/../assets';
    $imgPath = $assetsPath . '/img';
    $uploadsPath = $assetsPath . '/uploads';
    
    // Zip dosyası oluştur
    $zip = new ZipArchive();
    $zipFile = sys_get_temp_dir() . '/' . uniqid('assets_backup_') . '.zip';
    
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        throw new Exception('Zip dosyası oluşturulamadı');
    }
    
    // Progress tracking için dosya sayısını hesapla
    $totalFiles = 0;
    $filesProcessed = 0;
    
    // Dosya sayısını hesapla
    function countFiles($dir) {
        $count = 0;
        if (is_dir($dir)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
    $totalFiles += countFiles($imgPath);
    $totalFiles += countFiles($uploadsPath);
    
    // Output buffering
    ob_start();
    
    // assets/img klasörünü ekle
    if (is_dir($imgPath)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($imgPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = 'img/' . substr($filePath, strlen($imgPath) + 1);
                $zip->addFile($filePath, $relativePath);
                $filesProcessed++;
                
                // Flush output for streaming
                if ($filesProcessed % 10 == 0) {
                    ob_flush();
                    flush();
                }
            }
        }
    }
    
    // assets/uploads klasörünü ekle
    if (is_dir($uploadsPath)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadsPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = 'uploads/' . substr($filePath, strlen($uploadsPath) + 1);
                $zip->addFile($filePath, $relativePath);
                $filesProcessed++;
                
                // Flush output for streaming
                if ($filesProcessed % 10 == 0) {
                    ob_flush();
                    flush();
                }
            }
        }
    }
    
    // Zip dosyasını kapat
    $zip->close();
    
    // Zip dosyasını oku ve gönder
    if (file_exists($zipFile)) {
        $fileSize = filesize($zipFile);
        header('Content-Length: ' . $fileSize);
        
        // Dosyayı oku ve gönder
        $handle = fopen($zipFile, 'rb');
        if ($handle) {
            while (!feof($handle)) {
                echo fread($handle, 8192); // 8KB chunks
                ob_flush();
                flush();
            }
            fclose($handle);
        }
        
        // Geçici dosyayı sil
        @unlink($zipFile);
    } else {
        throw new Exception('Zip dosyası oluşturulamadı');
    }
    
    ob_end_flush();
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Assets yedekleme hatası: ' . $e->getMessage()
    ]);
    exit;
}

