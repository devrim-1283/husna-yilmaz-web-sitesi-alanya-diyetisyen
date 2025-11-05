<?php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel'; ?> - Diyetisyen Hüsna Yılmaz</title>
    
    <!-- Favicon - Ana sitedeki ile aynı -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" style="text-decoration: none; color: inherit;">
                    <h2>Admin Panel</h2>
                </a>
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="analytics.php" class="<?php echo $current_page == 'analytics' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Analitik
        </a>
        <a href="tool-stats.php" class="<?php echo $current_page == 'tool-stats' ? 'active' : ''; ?>">
            <i class="fas fa-calculator"></i> Araçlar İstatistikleri
        </a>
        <a href="appointments.php" class="<?php echo $current_page == 'appointments' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i> Randevular
        </a>
        <a href="messages.php" class="<?php echo $current_page == 'messages' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Mesajlar
        </a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                <a href="services.php" class="<?php echo $current_page == 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-concierge-bell"></i> Hizmetler
                </a>
                <a href="certificates.php" class="<?php echo $current_page == 'certificates' ? 'active' : ''; ?>">
                    <i class="fas fa-certificate"></i> Sertifikalar
                </a>
                <a href="success-stories.php" class="<?php echo $current_page == 'success-stories' ? 'active' : ''; ?>">
                    <i class="fas fa-trophy"></i> Başarı Hikayeleri
                </a>
                <a href="blogs.php" class="<?php echo $current_page == 'blogs' ? 'active' : ''; ?>">
                    <i class="fas fa-blog"></i> Bloglar
                </a>
                <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                <a href="settings.php" class="<?php echo $current_page == 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Ayarlar
                </a>
                <a href="users.php" class="<?php echo $current_page == 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Kullanıcılar
                </a>
                <hr>
                <a href="/" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
                </a>
                <a href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <header class="admin-header">
                <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><?php echo $page_title ?? 'Admin Panel'; ?></h1>
            </header>
            
            <div class="admin-content">

