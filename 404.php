<?php
require_once __DIR__ . '/config/config.php';

$page_title = '404 - Sayfa Bulunamadı';
$page_description = 'Aradığınız sayfa bulunamadı.';

http_response_code(404);

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container text-center">
        <h1 style="font-size: 120px; color: var(--primary-green); margin-bottom: 20px;">404</h1>
        <h2>Sayfa Bulunamadı</h2>
        <p style="font-size: 18px; color: var(--text-gray); margin: 20px 0;">
            Aradığınız sayfa mevcut değil veya taşınmış olabilir.
        </p>
        <div style="margin-top: 30px;">
            <a href="/" class="btn btn-primary">Ana Sayfaya Dön</a>
            <a href="/contact" class="btn btn-outline">İletişime Geç</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

