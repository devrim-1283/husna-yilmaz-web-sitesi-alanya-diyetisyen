<?php
http_response_code(500);
require_once __DIR__ . '/config/config.php';

$page_title = '500 - Sunucu Hatası';
$page_description = 'Bir sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 100px; min-height: 60vh;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 8rem; font-weight: 700; color: var(--primary-green); margin-bottom: 20px;">500</h1>
            <h2 style="font-size: 2rem; margin-bottom: 20px;">Sunucu Hatası</h2>
            <p style="font-size: 1.2rem; color: var(--text-gray); margin-bottom: 40px;">
                Bir sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i> Ana Sayfaya Dön
                </a>
                <button onclick="history.back()" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </button>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

