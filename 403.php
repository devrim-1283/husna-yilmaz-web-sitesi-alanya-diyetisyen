<?php
http_response_code(403);
require_once __DIR__ . '/config/config.php';

$page_title = '403 - Erişim Reddedildi';
$page_description = 'Bu sayfaya erişim yetkiniz bulunmamaktadır.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 100px; min-height: 60vh;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 8rem; font-weight: 700; color: var(--primary-green); margin-bottom: 20px;">403</h1>
            <h2 style="font-size: 2rem; margin-bottom: 20px;">Erişim Reddedildi</h2>
            <p style="font-size: 1.2rem; color: var(--text-gray); margin-bottom: 40px;">
                Bu sayfaya erişim yetkiniz bulunmamaktadır.
            </p>
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

