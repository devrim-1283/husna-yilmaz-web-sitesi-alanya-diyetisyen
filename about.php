<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Hakkımda sayfası ziyareti
trackPageView('hakkimda', $_SERVER['REQUEST_URI']);

$page_title = 'Hakkımda';
$page_description = 'Diyetisyen Hüsna Yılmaz hakkında bilgi edinin. Alanya\'da profesyonel beslenme danışmanlığı.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Ben Kimim Section -->
<section class="section" id="video" style="padding-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Hakkımda</h2>
            <p>Diyetisyen Hüsna Yılmaz ile tanışın</p>
        </div>
        
        <div class="about-content-wrapper">
            <div class="about-video-content fade-in-left">
                <div class="video-wrapper" style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
                    <img src="<?php echo imageUrl('assets/img/husna2.jpg'); ?>" 
                         alt="Diyetisyen Hüsna Yılmaz" 
                         style="width: 100%; height: auto; display: block; object-fit: cover;">
                </div>
            </div>
            
            <div class="about-text-content fade-in-right">
                <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 15px;">
                    Merhaba! Ben <strong>Diyetisyen Hüsna Yılmaz</strong>. 2020 yılında <strong>Ege Üniversitesi Beslenme ve Diyetetik</strong> 
                    bölümünden mezun oldum.
                </p>
                
                <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 15px;">
                    Şu anda <strong>Alanya Alaaddin Keykubat Üniversitesi (ALKÜ)</strong> Beslenme ve Diyetetik bölümünde 
                    <strong>tezli yüksek lisans</strong> eğitimime devam etmekteyim. Akademik çalışmalarımla mesleki 
                    bilgilerimi sürekli geliştiriyor, en güncel bilimsel yaklaşımları danışanlarımla buluşturuyorum.
                </p>
                
                <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 15px;">
                    Sağlıklı beslenmenin sadece kilo vermek değil, yaşam kalitesini artırmak olduğuna inanıyorum. 
                    Her bireyin kendine özgü ihtiyaçları olduğunu bilerek, <strong>kişiye özel beslenme programları</strong> 
                    hazırlıyorum.
                </p>
                
                <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                    Alanya'da profesyonel diyet ve beslenme danışmanlığı hizmeti sunuyorum. 
                    Modern bilimsel yaklaşımları doğal beslenme prensipleriyle birleştirerek, sürdürülebilir 
                    ve sağlıklı yaşam tarzı değişiklikleri için yanınızdayım.
                </p>
                
                <div class="about-highlights">
                    <div class="highlight-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Ege Üniversitesi Mezunu</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-user-graduate"></i>
                        <span>ALKÜ Yüksek Lisans</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Alanya</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Misyonum, Vizyonum Section -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Değerlerim</h2>
            <p>Profesyonel yaklaşımım ve ilkelerim</p>
        </div>
        
        <div class="grid grid-2">
            <div class="card fade-in-up">
                <div class="card-body" style="text-align: center; padding: 35px;">
                    <i class="fas fa-bullseye" style="font-size: 3.5rem; color: var(--primary-green); margin-bottom: 20px;"></i>
                    <h3 class="card-title" style="color: var(--primary-green); margin-bottom: 15px; font-size: 1.5rem;">Misyonum</h3>
                    <p class="card-text" style="font-size: 1.05rem; line-height: 1.7;">
                        Sağlıklı yaşam, doğru beslenme ile başlar. Amacım, her bireyin kendi yaşam tarzına uygun, 
                        sürdürülebilir ve sağlıklı beslenme alışkanlıkları edinmesine yardımcı olmaktır. 
                        Bilimsel ve kanıta dayalı beslenme danışmanlığı ile yanınızdayım.
                    </p>
                </div>
            </div>
            
            <div class="card fade-in-up">
                <div class="card-body" style="text-align: center; padding: 35px;">
                    <i class="fas fa-eye" style="font-size: 3.5rem; color: var(--primary-green); margin-bottom: 20px;"></i>
                    <h3 class="card-title" style="color: var(--primary-green); margin-bottom: 15px; font-size: 1.5rem;">Vizyonum</h3>
                    <p class="card-text" style="font-size: 1.05rem; line-height: 1.7;">
                        Toplumda sağlıklı beslenme bilincini artırmak ve herkesin kendi potansiyelinin farkına vararak 
                        sağlıklı bir yaşam sürmesini sağlamak. Modern beslenme bilimi ile doğal yaşamı birleştirerek 
                        öncü bir diyetisyen olmak.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Neden Ben Section -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Neden Ben?</h2>
            <p>Benimle çalışmanızın 6 önemli nedeni</p>
        </div>
        
        <div class="why-me-grid">
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="why-me-content">
                    <h3>Profesyonel Yaklaşım</h3>
                    <p>Bilimsel temellere dayanan, kanıta dayalı beslenme danışmanlığı ile yanınızdayım.</p>
                </div>
            </div>
            
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="why-me-content">
                    <h3>Kişiye Özel Programlar</h3>
                    <p>Her birey farklıdır. Size özel hazırlanan diyet programları ile hedeflerinize ulaşın.</p>
                </div>
            </div>
            
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="why-me-content">
                    <h3>Düzenli Takip ve Destek</h3>
                    <p>Süreç boyunca düzenli takip, motivasyon desteği ve gerektiğinde program güncellemeleri.</p>
                </div>
            </div>
            
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-laptop-medical"></i>
                </div>
                <div class="why-me-content">
                    <h3>Online Danışmanlık</h3>
                    <p>Nerede olursanız olun, online beslenme danışmanlığı ile uzaktan profesyonel destek.</p>
                </div>
            </div>
            
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="why-me-content">
                    <h3>Esnek Randevu Sistemi</h3>
                    <p>Size uygun zamanlarda randevu imkanı. Yoğun temponuza uygun çözümler.</p>
                </div>
            </div>
            
            <div class="why-me-item fade-in-up">
                <div class="why-me-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="why-me-content">
                    <h3>Genç ve Dinamik</h3>
                    <p>Güncel bilimsel verileri takip eden, sürekli kendini geliştiren bir diyetisyen olarak yanınızdayım.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certificates Section -->
<?php
$stmt = $pdo->prepare("SELECT * FROM certificates WHERE active = 1 ORDER BY order_position ASC");
$stmt->execute();
$certificates = $stmt->fetchAll();
?>
<section class="section bg-cream">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Sertifikalarım</h2>
            <p>Eğitim ve profesyonel gelişimim</p>
        </div>
        
        <?php if (count($certificates) > 0): ?>
            <div class="certificates-grid">
                <?php foreach ($certificates as $cert): ?>
                <div class="certificate-item fade-in-up">
                    <?php if ($cert['image']): ?>
                        <div class="certificate-image">
                            <img src="<?php echo imageUrl('assets/' . $cert['image']); ?>" 
                                 alt="<?php echo clean($cert['title']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="certificate-image certificate-placeholder">
                            <i class="fas fa-certificate"></i>
                        </div>
                    <?php endif; ?>
                    <div class="certificate-info">
                        <h3><?php echo clean($cert['title']); ?></h3>
                        <?php if ($cert['description']): ?>
                            <p><?php echo clean($cert['description']); ?></p>
                        <?php endif; ?>
                        <?php if ($cert['issue_date']): ?>
                            <div class="certificate-date">
                                <i class="far fa-calendar"></i>
                                <span><?php echo date('Y', strtotime($cert['issue_date'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-certificates fade-in-up">
                <i class="fas fa-certificate"></i>
                <p>Görüntülenecek sertifika yok</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

