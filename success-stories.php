<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Başarı Hikayeleri sayfası ziyareti
trackPageView('basari_hikayeleri', $_SERVER['REQUEST_URI']);

$page_title = 'Başarı Hikayeleri';
$page_description = 'Diyetisyen Hüsna Yılmaz ile hedeflerine ulaşan danışanların başarı hikayeleri.';

// Tüm aktif başarı hikayelerini getir
$stmt = $pdo->prepare("SELECT * FROM success_stories WHERE active = 1 ORDER BY order_position ASC");
$stmt->execute();
$stories = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Success Stories Section -->
<section class="section" style="padding-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Başarı Hikayeleri</h2>
            <p>Hedeflerine ulaşan danışanlarımın ilham verici hikayeleri</p>
        </div>
        
        <?php if (count($stories) > 0): ?>
            <div class="grid grid-3">
                <?php foreach ($stories as $story): ?>
                <a href="/success-story/<?php echo $story['id']; ?>/<?php echo slugify($story['title']); ?>" class="card fade-in-up" style="text-decoration: none; color: inherit; transition: transform 0.3s, box-shadow 0.3s; display: block;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                    <!-- Cover Image -->
                    <?php if ($story['cover_image']): ?>
                    <div style="position: relative; overflow: hidden;">
                        <img src="<?php echo imageUrl('assets/' . $story['cover_image']); ?>" 
                             alt="<?php echo clean($story['title']); ?>" 
                             style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s;" 
                             onmouseover="this.style.transform='scale(1.05)'" 
                             onmouseout="this.style.transform='scale(1)'">
                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(46, 125, 50, 0.9); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            <i class="fas fa-images"></i> Önce & Sonra
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Fallback: Cover yoksa before/after yan yana -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; position: relative;">
                        <?php if ($story['before_image']): ?>
                        <div style="position: relative;">
                            <img src="<?php echo imageUrl('assets/' . $story['before_image']); ?>" 
                                 alt="Önce" 
                                 style="width: 100%; height: 250px; object-fit: cover;">
                            <div style="position: absolute; top: 10px; left: 10px; background: rgba(231, 76, 60, 0.9); color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                Önce
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($story['after_image']): ?>
                        <div style="position: relative;">
                            <img src="<?php echo imageUrl('assets/' . $story['after_image']); ?>" 
                                 alt="Sonra" 
                                 style="width: 100%; height: 250px; object-fit: cover;">
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(46, 125, 50, 0.9); color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                Sonra
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="card-title"><?php echo clean($story['title']); ?></h3>
                        <p class="card-text"><?php echo truncate($story['content'], 120); ?></p>
                        <div style="margin-top: 15px; text-align: center; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                            <span style="color: var(--primary); font-weight: 600; font-size: 14px;">
                                <i class="fas fa-book-open"></i> Hikayeyi Oku
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center fade-in-up">
                <i class="fas fa-trophy" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 20px; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem; color: var(--text-gray);">Şu anda görüntülenecek başarı hikayesi bulunmamaktadır.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-cream">
    <div class="container">
        <div class="cta-card fade-in-up">
            <div class="cta-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="cta-content">
                <h2>Siz de Başarı Hikayenizi Yazın!</h2>
                <p>Profesyonel destek ile hedeflerinize ulaşın ve yaşam kalitenizi artırın</p>
            </div>
            <div class="cta-action">
                <a href="/contact" class="btn btn-primary">
                    <i class="fas fa-rocket"></i> Hemen Başlayın
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

