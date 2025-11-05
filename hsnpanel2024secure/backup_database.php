<?php
/**
 * Database Backup Script
 * Tüm veritabanını SQL dosyası olarak export eder
 */

require_once __DIR__ . '/../config/config.php';
requireAdmin();

// CSRF token kontrolü
if (!isset($_GET['token']) || !verifyCsrfToken($_GET['token'])) {
    http_response_code(403);
    die('Unauthorized');
}

// Database bilgileri
$host = DB_HOST;
$port = DB_PORT;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASS;

// Tarih formatı
$date = date('Y-m-d_H-i-s');
$filename = "backup_{$dbname}_{$date}.sql";

// Headers - file download için
header('Content-Type: application/octet-stream');
header('Cache-Control: no-cache, must-revalidate');
header('X-Accel-Buffering: no'); // Disable buffering for nginx

try {
    // PDO bağlantısı
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // SQL başlangıcı
    $output = "-- Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database: {$dbname}\n";
    $output .= "-- Host: {$host}:{$port}\n\n";
    
    $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $output .= "START TRANSACTION;\n";
    $output .= "SET time_zone = \"+00:00\";\n\n";
    
    // Tüm tabloları listele
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $totalTables = count($tables);
    
    // Output buffering - streaming için
    ob_start();
    
    // Her tablo için
    $tableIndex = 0;
    foreach ($tables as $table) {
        $tableIndex++;
        
        // Tablo yapısını al
        $output .= "\n-- --------------------------------------------------------\n";
        $output .= "-- Table structure for table `{$table}`\n";
        $output .= "-- --------------------------------------------------------\n\n";
        
        $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $createTable = $stmt->fetch();
        $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $output .= $createTable['Create Table'] . ";\n\n";
        
        // Tablo verilerini al
        $output .= "-- Dumping data for table `{$table}`\n\n";
        
        $stmt = $pdo->query("SELECT * FROM `{$table}`");
        $rows = $stmt->fetchAll();
        
        if (count($rows) > 0) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
            
            $rowCount = count($rows);
            foreach ($rows as $index => $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $value = addslashes($value);
                        $value = str_replace("\n", "\\n", $value);
                        $value = str_replace("\r", "\\r", $value);
                        $values[] = "'{$value}'";
                    }
                }
                
                $comma = ($index < $rowCount - 1) ? ',' : ';';
                $output .= "(" . implode(', ', $values) . "){$comma}\n";
            }
        }
        
        $output .= "\n";
        
        // Flush output for streaming
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
    
    $output .= "-- --------------------------------------------------------\n";
    $output .= "COMMIT;\n";
    
    // Dosya header'ları
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Content-Length: ' . strlen($output));
    
    echo $output;
    ob_end_flush();
    exit;
    
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Veritabanı yedekleme hatası: ' . $e->getMessage()
    ]);
    exit;
}

