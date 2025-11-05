<?php
/**
 * Main Entry Point with Router Support
 * For clean URLs, router.php handles routing
 */

// Router support for clean URLs (only if not root path)
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestPath = rtrim($requestPath, '/');

if ($requestPath !== '' && $requestPath !== '/' && $requestPath !== '/index.php') {
    // Check if router exists and handle clean URLs
    if (file_exists(__DIR__ . '/router.php')) {
        $routerHandled = require_once __DIR__ . '/router.php';
        if ($routerHandled === true) {
            exit; // Router handled the request
        }
        // If router returned false, show 404
        if ($routerHandled === false) {
            http_response_code(404);
            require_once __DIR__ . '/404.php';
            exit;
        }
    }
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

$page_title = 'Ana Sayfa';
$page_description = 'Alanya\'da profesyonel diyet ve beslenme danışmanlığı. Kişisel diyet programları, online danışmanlık.';
$page_keywords = 'Alanya diyetisyen, Alanya sağlıklı beslenme, Alanya diyet danışmanı, beslenme uzmanı Alanya';

// Analytics: Ana sayfa ziyareti kaydet (hata olsa bile devam et)
try {
    trackPageView('ana_sayfa', $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage());
    // Analytics hatası siteyi durdurmamalı
}

// Ana sayfadaki hizmetleri getir (ilk 3)
$services = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE active = 1 ORDER BY order_position ASC LIMIT 3");
    $stmt->execute();
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Services query error: " . $e->getMessage());
    // Tablo yoksa boş array kullan
}

// Başarı hikayelerini getir (ilk 3)
$stories = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM success_stories WHERE active = 1 ORDER BY order_position ASC LIMIT 3");
    $stmt->execute();
    $stories = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Success stories query error: " . $e->getMessage());
    // Tablo yoksa boş array kullan
}

// Son blogları getir (ilk 3)
$blogs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE active = 1 ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Blogs query error: " . $e->getMessage());
    // Tablo yoksa boş array kullan
}

// Randevu formu işleme
$form_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_submit'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if ($name && $phone && $message) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, phone, message) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $phone, $message])) {
                    $form_message = 'success';
                } else {
                    $form_message = 'error';
                }
            } catch (PDOException $e) {
                error_log("Contact message insert error: " . $e->getMessage());
                $form_message = 'error';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <img src="/assets/img/hh.jpg" alt="Diyetisyen Hüsna Yılmaz" class="hero-bg">
    <div class="hero-content">
        <h1>Sağlıklı Yaşam Danışmanınız</h1>
        <p>Kişiye özel diyet programları ve profesyonel beslenme danışmanlığı ile hedeflerinize ulaşın</p>
        <div class="hero-buttons">
            <div class="hero-buttons-top">
                <a href="/contact#randevu" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i>
                    Hemen Randevu Al
                </a>
                <a href="/about#video" class="btn btn-outline" style="color: white; border-color: white;">
                    <i class="fas fa-play-circle"></i>
                    Beni Tanı
                </a>
            </div>
            <a href="/vki" class="btn btn-outline hero-vki-btn" style="color: white; border-color: white;">
                <i class="fas fa-calculator"></i>
                Vücut Kitle İndeksi Hesapla
            </a>
        </div>
    </div>
</section>

<!-- Beni Tanıyın Section -->
<section class="section about-intro">
    <div class="container">
        <div class="about-intro-content">
            <div class="about-intro-image fade-in-left">
                <img src="/assets/img/husna.png" alt="Diyetisyen Hüsna Yılmaz" class="profile-img">
            </div>
            <div class="about-intro-text fade-in-right">
                <h2>Beni Tanıyın</h2>
                <p class="intro-lead">Merhaba! Ben Diyetisyen Hüsna Yılmaz</p>
                <p>
                    2025 yılında Alanya Alaaddin Keykubat Üniversitesi Beslenme ve Diyetetik bölümünden mezun oldum. 
                    21 yaşında, genç ve dinamik bir diyetisyen olarak sizlere hizmet veriyorum.
                </p>
                <p>
                    Sağlıklı beslenmenin sadece kilo vermek değil, yaşam kalitesini artırmak olduğuna inanıyorum. 
                    Her bireyin kendine özgü ihtiyaçları olduğunu bilerek, kişiye özel beslenme programları hazırlıyorum.
                </p>
                <p>
                    Modern bilimsel yaklaşımları doğal beslenme prensipleriyle birleştirerek, sürdürülebilir 
                    ve sağlıklı yaşam tarzı değişiklikleri için yanınızdayım.
                </p>
                <div class="about-highlights">
                    <div class="highlight-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>ALKÜ Beslenme ve Diyetetik</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>2025 Mezunu</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Alanya</span>
                    </div>
                </div>
                <a href="/about" class="btn btn-primary">
                    <i class="fas fa-info-circle"></i>
                    Daha Fazla Bilgi
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Hizmetlerim Section -->
<section class="section services-section">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Hizmetlerim</h2>
            <p>Size en uygun beslenme programı ile sağlıklı yaşama adım atın</p>
        </div>
        
        <div class="grid grid-3">
            <div class="card fade-in-up">
                <div class="card-body">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-apple-alt" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                        <h3 class="card-title">Kişiye Özel Diyet</h3>
                        <p class="card-text">Hedeflerinize özel hazırlanan beslenme programları ile sağlıklı yaşam yolculuğunuza başlayın.</p>
                    </div>
                </div>
            </div>
            <div class="card fade-in-up">
                <div class="card-body">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-laptop-medical" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                        <h3 class="card-title">Online Danışmanlık</h3>
                        <p class="card-text">Nerede olursanız olun, online beslenme danışmanlığı ile uzaktan profesyonel destek alın.</p>
                    </div>
                </div>
            </div>
            <div class="card fade-in-up">
                <div class="card-body">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-heartbeat" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                        <h3 class="card-title">Sağlıklı Yaşam</h3>
                        <p class="card-text">Beslenme alışkanlıklarınızı değiştirerek kalıcı ve sağlıklı bir yaşam tarzı edinin.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 fade-in-up">
            <a href="/services" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                Tüm Hizmetleri Gör
            </a>
        </div>
    </div>
</section>

<!-- Başarı Hikayeleri Section -->
<section class="section bg-cream success-section">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Başarı Hikayeleri</h2>
            <p>Hedeflerine ulaşan danışanlarımın başarı hikayeleri</p>
        </div>
        
        <div class="grid grid-3">
            <?php if (count($stories) > 0): ?>
                <?php foreach ($stories as $story): ?>
                <div class="card fade-in-up" style="cursor: pointer; overflow: hidden;" onclick="openStoryModal(<?php echo $story['id']; ?>)">
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
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo clean($story['title']); ?></h3>
                        <p class="card-text"><?php echo truncate(clean($story['content']), 120); ?></p>
                        <div style="margin-top: 15px; text-align: center; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                            <span style="color: var(--primary); font-weight: 600; font-size: 14px;">
                                <i class="fas fa-eye"></i> Detayları Gör
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder cards -->
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-trophy" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">15 Kilo Verdim</h3>
                            <p class="card-text">3 ayda 15 kilo vererek hedefime ulaştım. Artık kendimi çok daha enerjik ve sağlıklı hissediyorum.</p>
                        </div>
                    </div>
                </div>
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-medal" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">Sağlıklı Beslenme</h3>
                            <p class="card-text">Beslenme alışkanlıklarımı tamamen değiştirdim. Artık daha bilinçli ve dengeli besleniyor, kendimi harika hissediyorum.</p>
                        </div>
                    </div>
                </div>
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-star" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">Yaşam Tarzı Değişimi</h3>
                            <p class="card-text">Sadece kilo vermedim, yaşam tarzımı değiştirdim. Artık daha aktif ve mutluyum. Teşekkürler!</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4 fade-in-up">
            <a href="/success-stories" class="btn btn-primary">
                <i class="fas fa-trophy"></i>
                Tüm Başarı Hikayelerini Gör
            </a>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="section blog-section">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Son Yazılar</h2>
            <p>Sağlıklı yaşam ve beslenme hakkında bilgilendirici yazılar</p>
        </div>
        
        <div class="grid grid-3">
            <?php if (count($blogs) > 0): ?>
                <?php foreach ($blogs as $blog): ?>
                <a href="/blog/<?php echo $blog['id']; ?>/<?php echo slugify($blog['title']); ?>" class="card fade-in-up" style="text-decoration: none; color: inherit; transition: transform 0.3s, box-shadow 0.3s; display: block;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                    <?php if ($blog['image']): ?>
                    <img src="<?php echo imageUrl('assets/' . $blog['image']); ?>" 
                         alt="<?php echo clean($blog['title']); ?>" 
                         style="width: 100%; height: 200px; object-fit: cover;" 
                         loading="lazy">
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo clean($blog['title']); ?></h3>
                        <p class="card-text"><?php echo truncate(clean($blog['meta_description'] ?? strip_tags($blog['content'])), 120); ?></p>
                        <div class="card-meta" style="margin-top: 15px;">
                            <span><i class="far fa-calendar"></i> <?php echo date('d.m.Y', strtotime($blog['created_at'])); ?></span>
                            <span><i class="far fa-eye"></i> <?php echo $blog['views']; ?> görüntülenme</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder cards -->
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-leaf" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">Sağlıklı Beslenme İpuçları</h3>
                            <p class="card-text">Günlük hayatınızda uygulayabileceğiniz pratik ve etkili beslenme önerileri ile sağlıklı yaşam.</p>
                            <div class="card-meta" style="margin-top: 15px;">
                                <span><i class="far fa-calendar"></i> Yakında</span>
                                <span><i class="far fa-clock"></i> 5 dk okuma</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-carrot" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">Mevsim Sebzeleri Rehberi</h3>
                            <p class="card-text">Mevsiminde tüketilen sebzelerin faydaları ve lezzetli tariflerle sağlıklı beslenme rehberiniz.</p>
                            <div class="card-meta" style="margin-top: 15px;">
                                <span><i class="far fa-calendar"></i> Yakında</span>
                                <span><i class="far fa-clock"></i> 7 dk okuma</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card fade-in-up">
                    <div class="card-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-dumbbell" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                            <h3 class="card-title">Spor ve Beslenme</h3>
                            <p class="card-text">Egzersiz yaparken doğru beslenme stratejileri ile performansınızı artırın ve hedeflerinize ulaşın.</p>
                            <div class="card-meta" style="margin-top: 15px;">
                                <span><i class="far fa-calendar"></i> Yakında</span>
                                <span><i class="far fa-clock"></i> 6 dk okuma</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4 fade-in-up">
            <a href="/blog" class="btn btn-primary">
                <i class="fas fa-book-open"></i>
                Tüm Yazıları Gör
            </a>
        </div>
    </div>
</section>

<!-- Randevu Section -->
<section class="section bg-cream appointment-section">
    <div class="container">
        <div class="appointment-wrapper">
            <!-- Randevu Bilgi -->
            <div class="intro-content fade-in-left">
                <div class="appointment-info-card">
                    <div class="appointment-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h2>Randevu Oluşturun</h2>
                    <p class="appointment-highlight">🌿 Kişiye Özel Beslenme Programları</p>
                    <p>Profesyonel diyet ve beslenme danışmanlığı ile sağlıklı yaşam yolculuğunuzda yanınızdayım.</p>
                    <div class="appointment-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Hızlı ve Kolay Randevu</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>WhatsApp ile Anlık İletişim</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Kişiye Özel Program</span>
                        </div>
                    </div>
                    
                    <!-- İletişim Bilgileri -->
                    <div class="contact-info-cards">
                        <a href="tel:+905536998982" class="contact-card">
                            <div class="contact-card-icon phone-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-card-info">
                                <span class="contact-label">Telefon</span>
                                <span class="contact-value">+90 553 699 89 82</span>
                            </div>
                        </a>
                        
                        <a href="https://wa.me/905536998982" target="_blank" class="contact-card">
                            <div class="contact-card-icon whatsapp-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-card-info">
                                <span class="contact-label">WhatsApp</span>
                                <span class="contact-value">Mesaj Gönder</span>
                            </div>
                        </a>
                        
                        <a href="https://maps.app.goo.gl/A2TD94jUF7dr514F6" target="_blank" class="contact-card">
                            <div class="contact-card-icon location-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="contact-card-info">
                                <span class="contact-label">Konum</span>
                                <span class="contact-value">Alanya</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Randevu Formu -->
            <div class="appointment-form fade-in-right">
                <div class="form-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h2>Randevu Formu</h2>
                </div>
                
                <form id="appointmentForm" onsubmit="submitAppointment(event); return false;">
                    <div class="form-group">
                        <label class="form-label" for="firstName">Adınız *</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="lastName">Soyadınız *</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Telefon *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+90 5XX XXX XX XX" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="appointmentDate">Uygun Olduğunuz Tarih ve Saat *</label>
                        <input type="datetime-local" id="appointmentDate" name="appointmentDate" class="form-control" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Mesajınız <small style="color: #6c757d;">(Maks. 200 karakter)</small></label>
                        <textarea id="message" name="message" class="form-control" rows="3" maxlength="200" placeholder="Ek notlarınız varsa buraya yazabilirsiniz..."></textarea>
                        <small style="color: #6c757d; font-size: 11px;">
                            <span id="charCount">0</span> / 200 karakter
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i>
                        Randevu Talebi Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Contact Preview Section -->
<section class="section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 80px 0;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2><i class="fas fa-map-marker-alt" style="color: var(--primary-green); margin-right: 10px;"></i> İletişim & Konum</h2>
            <p>Bize ulaşın, sağlıklı yaşam yolculuğunuzda yanınızdayız</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 40px; max-width: 1200px; margin: 0 auto;">
            <!-- Sol Taraf: Harita -->
            <div class="fade-in-left" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3205.441130387499!2d32.000434!3d36.543475199999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14dc996cca53536d%3A0xafb7006971068a25!2zRGl5ZXRpc3llbiBIw7xzbmEgWcSxbG1heiBCZXNsZW5tZSBEYW7EscWfbWFubMSxayBNZXJrZXpp!5e0!3m2!1str!2str!4v1761087215309!5m2!1str!2str"
                    style="width: 100%; height: 500px; border: 0;"
                    allowfullscreen="" 
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            
            <!-- Sağ Taraf: İletişim Bilgileri -->
            <div class="fade-in-right" style="display: flex; flex-direction: column; gap: 20px;">
                <!-- İletişim Kartları -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                    <!-- Telefon -->
                    <a href="tel:+905536998982" style="text-decoration: none;">
                        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; display: flex; align-items: center; gap: 15px; height: 100%;" class="hover-card">
                            <div style="width: 50px; height: 50px; min-width: 50px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-phone-alt" style="font-size: 20px; color: white;"></i>
                            </div>
                            <div>
                                <h4 style="color: var(--primary-green); margin: 0 0 5px 0; font-size: 1rem;">Telefon</h4>
                                <p style="color: var(--primary-green); margin: 0; font-size: 0.95rem; font-weight: 600;">+90 553 699 89 82</p>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Adres -->
                    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; display: flex; align-items: center; gap: 15px;" class="hover-card">
                        <div style="width: 50px; height: 50px; min-width: 50px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt" style="font-size: 20px; color: white;"></i>
                        </div>
                        <div>
                            <h4 style="color: var(--primary-green); margin: 0 0 5px 0; font-size: 1rem;">Adres</h4>
                            <p style="color: var(--text-gray); margin: 0; font-size: 0.9rem;">Alanya, Antalya</p>
                        </div>
                    </div>
                    
                    <!-- E-posta -->
                    <a href="mailto:destek@husnayilmaz.com" style="text-decoration: none;">
                        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; display: flex; align-items: center; gap: 15px; height: 100%;" class="hover-card">
                            <div style="width: 50px; height: 50px; min-width: 50px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope" style="font-size: 20px; color: white;"></i>
                            </div>
                            <div>
                                <h4 style="color: var(--primary-green); margin: 0 0 5px 0; font-size: 1rem;">E-posta</h4>
                                <p style="color: var(--primary-green); margin: 0; font-size: 0.9rem; font-weight: 600;">destek@husnayilmaz.com</p>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Çalışma Saatleri -->
                    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; display: flex; align-items: flex-start; gap: 15px;" class="hover-card">
                        <div style="width: 50px; height: 50px; min-width: 50px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="font-size: 20px; color: white;"></i>
                        </div>
                        <div>
                            <h4 style="color: var(--primary-green); margin: 0 0 10px 0; font-size: 1rem;">Çalışma Saatleri</h4>
                            <p style="color: var(--text-gray); margin: 0; font-size: 0.85rem; line-height: 1.8;">
                                <strong>Pzt-Cmt:</strong> 09:00-17:30<br>
                                <strong>Pazar:</strong> <span style="color: #dc3545;">Kapalı</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Hızlı İletişim Butonları -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="https://wa.me/905536998982" target="_blank" class="btn" style="flex: 1; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); display: flex; align-items: center; justify-content: center; gap: 8px; padding: 15px;">
                        <i class="fab fa-whatsapp" style="font-size: 18px;"></i>
                        WhatsApp
                    </a>
                    <a href="/contact" class="btn btn-primary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 15px;">
                        <i class="fas fa-envelope"></i>
                        Mesaj Gönder
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}
.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
.btn-sm {
    padding: 8px 20px;
    font-size: 14px;
}
</style>

<!-- Success Story Modal -->
<div id="storyModal" class="modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8);">
    <div class="modal-content" style="background: white; margin: 50px auto; padding: 0; max-width: 900px; border-radius: 15px; position: relative; animation: slideDown 0.3s;">
        <span class="modal-close" onclick="closeStoryModal()" style="position: absolute; top: 15px; right: 20px; color: white; font-size: 35px; font-weight: bold; cursor: pointer; z-index: 10; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">&times;</span>
        
        <div id="storyModalContent"></div>
    </div>
</div>

<script>
// Success Story Modal Functions
const storiesData = <?php echo json_encode($stories); ?>;

function openStoryModal(storyId) {
    const story = storiesData.find(s => s.id == storyId);
    if (!story) return;
    
    const modalContent = document.getElementById('storyModalContent');
    modalContent.innerHTML = `
        <h2 style="padding: 30px 30px 20px; margin: 0; color: var(--text-dark);">${story.title}</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 1px solid #e0e0e0;">
            ${story.before_image ? `
            <div style="position: relative;">
                <img src="/assets/${story.before_image}?v=${Date.now()}" 
                     alt="Önce" 
                     style="width: 100%; height: 300px; object-fit: cover;">
                <div style="position: absolute; top: 15px; left: 15px; background: rgba(231, 76, 60, 0.9); color: white; padding: 8px 16px; border-radius: 25px; font-size: 14px; font-weight: 700;">
                    Önce
                </div>
            </div>
            ` : ''}
            
            ${story.after_image ? `
            <div style="position: relative;">
                <img src="/assets/${story.after_image}?v=${Date.now()}" 
                     alt="Sonra" 
                     style="width: 100%; height: 300px; object-fit: cover;">
                <div style="position: absolute; top: 15px; right: 15px; background: rgba(46, 125, 50, 0.9); color: white; padding: 8px 16px; border-radius: 25px; font-size: 14px; font-weight: 700;">
                    Sonra
                </div>
            </div>
            ` : ''}
        </div>
        
        <div style="padding: 30px;">
            <p style="font-size: 16px; line-height: 1.8; color: var(--text-gray); white-space: pre-wrap;">${story.content}</p>
        </div>
    `;
    
    document.getElementById('storyModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStoryModal() {
    document.getElementById('storyModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('storyModal');
    if (event.target == modal) {
        closeStoryModal();
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStoryModal();
    }
});
</script>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-close:hover {
    color: #f1f1f1 !important;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    #storyModal .modal-content {
        margin: 20px;
        max-width: calc(100% - 40px);
    }
    
    #storyModal .modal-content > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

