<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: İletişim sayfası ziyareti
trackPageView('iletisim', $_SERVER['REQUEST_URI']);

$page_title = 'İletişim';
$page_description = 'Randevu almak veya bilgi almak için benimle iletişime geçin. Alanya diyetisyen.';

// Form işleme
$form_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if ($name && $message && ($email || $phone)) {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message, type) VALUES (?, ?, ?, ?, 'message')");
            if ($stmt->execute([$name, $email, $phone, $message])) {
                $form_message = 'success';
                
                // E-posta gönderimi (opsiyonel)
                // mail(CONTACT_EMAIL, "Yeni İletişim Mesajı", $message, "From: $email");
            } else {
                $form_message = 'error';
            }
        } else {
            $form_message = 'error';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Contact Section -->
<section class="section" style="padding-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>İletişim</h2>
            <p>Randevu almak veya bilgi almak için benimle iletişime geçin</p>
        </div>
        
        <div class="contact-wrapper">
            <!-- Contact Info Cards -->
            <div class="contact-info-grid fade-in-left">
                <div class="contact-info-card">
                    <div class="contact-info-card-icon location-gradient">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-info-card-content">
                        <h3>Adres</h3>
                        <p>Alanya, Antalya</p>
                    </div>
                </div>
                
                <div class="contact-info-card">
                    <div class="contact-info-card-icon phone-gradient">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-info-card-content">
                        <h3>Telefon</h3>
                        <a href="tel:+905536998982">+90 553 699 89 82</a>
                    </div>
                </div>
                
                <div class="contact-info-card">
                    <div class="contact-info-card-icon email-gradient">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info-card-content">
                        <h3>E-posta</h3>
                        <a href="mailto:destek@husnayilmaz.com">destek@husnayilmaz.com</a>
                    </div>
                </div>
                
                <div class="contact-info-card">
                    <div class="contact-info-card-icon time-gradient">
                        <i class="far fa-clock"></i>
                    </div>
                    <div class="contact-info-card-content">
                        <h3>Çalışma Saatleri</h3>
                        <div class="working-hours-list">
                            <div class="working-day">
                                <span class="day-name">Pazartesi</span>
                                <span class="day-hours">09:00 – 17:00</span>
                            </div>
                            <div class="working-day">
                                <span class="day-name">Salı</span>
                                <span class="day-hours">09:00 – 17:30</span>
                            </div>
                            <div class="working-day">
                                <span class="day-name">Çarşamba</span>
                                <span class="day-hours">09:00 – 17:30</span>
                            </div>
                            <div class="working-day">
                                <span class="day-name">Perşembe</span>
                                <span class="day-hours">09:00 – 17:30</span>
                            </div>
                            <div class="working-day">
                                <span class="day-name">Cuma</span>
                                <span class="day-hours">09:00 – 17:30</span>
                            </div>
                            <div class="working-day">
                                <span class="day-name">Cumartesi</span>
                                <span class="day-hours">09:00 – 14:00</span>
                            </div>
                            <div class="working-day closed">
                                <span class="day-name">Pazar</span>
                                <span class="day-hours">Kapalı</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-wrapper fade-in-right">
                <div class="contact-form-card">
                    <div class="contact-form-header">
                        <i class="fas fa-envelope-open-text"></i>
                        <h3>Mesaj Gönderin</h3>
                        <p>Size en kısa sürede dönüş yapacağım</p>
                    </div>
                    
                    <?php if ($form_message === 'success'): ?>
                        <div class="alert alert-success fade-in-up">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Teşekkürler!</strong>
                                <p>Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.</p>
                            </div>
                        </div>
                    <?php elseif ($form_message === 'error'): ?>
                        <div class="alert alert-error fade-in-up">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Hata!</strong>
                                <p>Bir hata oluştu. Lütfen tüm alanları doldurun ve tekrar deneyin.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" data-validate id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="fas fa-user"></i> Adınız Soyadınız *
                            </label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="Adınız ve soyadınız">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">
                                <i class="fas fa-envelope"></i> E-posta Adresiniz
                            </label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="ornek@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="phone">
                                <i class="fas fa-phone"></i> Telefon Numaranız *
                            </label>
                            <input type="tel" id="phone" name="phone" class="form-control" required placeholder="+90 5XX XXX XX XX">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message">
                                <i class="fas fa-comment-dots"></i> Mesajınız *
                            </label>
                            <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Mesajınızı buraya yazın..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Mesaj Gönder
                        </button>
                    </form>
                </div>
                
                <!-- Social Media -->
                <div class="social-media-card fade-in-up">
                    <h4>Sosyal Medya</h4>
                    <p>Beni sosyal medyadan da takip edebilirsiniz</p>
                    <div class="social-media-buttons">
                        <a href="https://wa.me/<?php echo clean($settings['whatsapp_number'] ?? '905536998982'); ?>" 
                           target="_blank" class="social-btn whatsapp-btn">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="<?php echo clean($settings['instagram_url'] ?? '#'); ?>" 
                           target="_blank" class="social-btn instagram-btn">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Konum</h2>
            <p>Ofisimizi haritada görüntüleyin</p>
        </div>
        <div class="fade-in-up" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 15px; box-shadow: var(--shadow);">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3205.441130387499!2d32.000434!3d36.543475199999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14dc996cca53536d%3A0xafb7006971068a25!2zRGl5ZXRpc3llbiBIw7xzbmEgWcSxbG1heiBCZXNsZW5tZSBEYW7EscWfbWFubMSxayBNZXJrZXpp!5e0!3m2!1str!2str!4v1761087215309!5m2!1str!2str"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- Randevu Section -->
<section class="appointment-section" id="randevu" style="margin-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up" style="margin-bottom: 60px;">
            <h2><i class="fas fa-calendar-check" style="margin-right: 10px; color: var(--primary-green);"></i> Randevu Oluştur</h2>
            <p>Size uygun tarih ve saatte randevu alabilirsiniz</p>
        </div>
        <div class="appointment-wrapper">
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
            <div id="randevu" class="appointment-form fade-in-right">
                <div class="form-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h2>Randevu Formu</h2>
                </div>
                
                <form id="appointmentForm" onsubmit="submitAppointment(event)">
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
                            <span id="charCount2">0</span> / 200 karakter
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>

