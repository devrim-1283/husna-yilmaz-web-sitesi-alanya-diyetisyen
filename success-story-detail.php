<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// ID'yi al
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Başarı hikayesini getir
$stmt = $pdo->prepare("SELECT * FROM success_stories WHERE id = ? AND active = 1");
$stmt->execute([$id]);
$story = $stmt->fetch();

// Hikaye bulunamadıysa 404
if (!$story) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/404.php';
    exit;
}

// Analytics: Başarı hikayesi detay sayfası ziyareti
trackPageView('basari_hikayesi_detay', $_SERVER['REQUEST_URI']);

$page_title = clean($story['title']);
$page_description = truncate(clean($story['content']), 160);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Success Story Detail -->
<section class="section" style="padding-top: 100px; padding-bottom: 60px;">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="fade-in-up" style="margin-bottom: 30px;">
            <a href="/" style="color: var(--text-gray); text-decoration: none;">
                <i class="fas fa-home"></i> Ana Sayfa
            </a>
            <span style="margin: 0 10px; color: var(--text-gray);">/</span>
            <a href="/success-stories" style="color: var(--text-gray); text-decoration: none;">
                Başarı Hikayeleri
            </a>
            <span style="margin: 0 10px; color: var(--text-gray);">/</span>
            <span style="color: var(--primary);"><?php echo clean($story['title']); ?></span>
        </div>
        
        <!-- Title -->
        <div class="fade-in-up" style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 2.5rem; color: var(--text-dark); margin-bottom: 15px;">
                <?php echo clean($story['title']); ?>
            </h1>
            <div style="width: 60px; height: 4px; background: var(--primary); margin: 0 auto;"></div>
        </div>
        
        <!-- Before/After Images -->
        <?php if ($story['before_image'] || $story['after_image']): ?>
        <div class="fade-in-up" style="margin-bottom: 50px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; max-width: 1000px; margin: 0 auto;">
                <!-- Before Image -->
                <?php if ($story['before_image']): ?>
                <div style="position: relative; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <img src="<?php echo imageUrl('assets/' . $story['before_image']); ?>" 
                         alt="Önce" 
                         style="width: 100%; height: auto; display: block;">
                    <div style="position: absolute; top: 20px; left: 20px; background: rgba(231, 76, 60, 0.95); color: white; padding: 10px 20px; border-radius: 30px; font-size: 16px; font-weight: 700; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
                        <i class="fas fa-arrow-left"></i> Önce
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- After Image -->
                <?php if ($story['after_image']): ?>
                <div style="position: relative; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <img src="<?php echo imageUrl('assets/' . $story['after_image']); ?>" 
                         alt="Sonra" 
                         style="width: 100%; height: auto; display: block;">
                    <div style="position: absolute; top: 20px; right: 20px; background: rgba(46, 125, 50, 0.95); color: white; padding: 10px 20px; border-radius: 30px; font-size: 16px; font-weight: 700; box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);">
                        Sonra <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Story Content -->
        <div class="fade-in-up" style="max-width: 800px; margin: 0 auto;">
            <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow);">
                <div style="font-size: 1.1rem; line-height: 1.9; color: var(--text-gray); white-space: pre-wrap;">
                    <?php echo nl2br(clean($story['content'])); ?>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="fade-in-up" style="text-align: center; margin-top: 50px; padding: 40px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-dark) 100%); border-radius: 15px; color: white;">
            <h3 style="margin-bottom: 15px; color: white; font-size: 1.8rem;">
                <i class="fas fa-heart"></i> Siz de Başarı Hikayenizi Yazmak İster misiniz?
            </h3>
            <p style="margin-bottom: 25px; font-size: 1.1rem; opacity: 0.95;">
                Kişiye özel diyet programları ile hedeflerinize ulaşın!
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/contact#randevu" class="btn" style="background: white; color: var(--primary); padding: 15px 35px; font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-calendar-check"></i> Hemen Randevu Al
                </a>
                <a href="/success-stories" class="btn btn-outline" style="border-color: white; color: white; padding: 15px 35px; font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-trophy"></i> Diğer Hikayeler
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Responsive Styles -->
<style>
@media (max-width: 768px) {
    section.section {
        padding-top: 80px !important;
    }
    
    section.section h1 {
        font-size: 1.8rem !important;
    }
    
    section.section > div > div:nth-child(3) > div {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    section.section > div > div:nth-child(4) > div {
        padding: 25px !important;
    }
    
    section.section > div > div:nth-child(5) {
        padding: 30px 20px !important;
    }
    
    section.section > div > div:nth-child(5) h3 {
        font-size: 1.3rem !important;
    }
    
    section.section > div > div:nth-child(5) > div {
        flex-direction: column !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

