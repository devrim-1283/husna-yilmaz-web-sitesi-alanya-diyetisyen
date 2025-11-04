<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Hizmetler sayfası ziyareti
trackPageView('hizmetler', $_SERVER['REQUEST_URI']);

$page_title = 'Hizmetlerim';
$page_description = 'Kişisel diyet programları, online danışmanlık ve daha fazlası. Alanya diyetisyen hizmetleri.';

// Tüm aktif hizmetleri getir
$stmt = $pdo->prepare("SELECT * FROM services WHERE active = 1 ORDER BY order_position ASC");
$stmt->execute();
$services = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Services Section -->
<section class="section" style="padding-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Hizmetlerim</h2>
            <p>Size özel beslenme çözümleri sunuyorum</p>
        </div>
        
        <?php if (count($services) > 0): ?>
            <div class="grid grid-3">
                <?php foreach ($services as $service): ?>
                <div class="card fade-in-up">
                    <?php if ($service['image']): ?>
                        <img src="<?php echo imageUrl('assets/' . $service['image']); ?>" 
                             alt="<?php echo clean($service['title']); ?>" 
                             class="card-img" 
                             loading="eager"
                             onerror="console.error('Resim yüklenemedi:', this.src);">
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo clean($service['title']); ?></h3>
                        <p class="card-text"><?php echo nl2br(clean($service['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center fade-in-up">
                <i class="fas fa-spa" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 20px; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem; color: var(--text-gray);">Şu anda görüntülenecek hizmet bulunmamaktadır.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-cream">
    <div class="container">
        <div class="cta-card fade-in-up">
            <div class="cta-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="cta-content">
                <h2>Hemen Başlayın!</h2>
                <p>Size özel diyet programı için randevu alın ve sağlıklı yaşam yolculuğunuza başlayın</p>
            </div>
            <div class="cta-action">
                <a href="/contact" class="btn btn-primary">
                    <i class="fas fa-phone-alt"></i> Randevu Al
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

