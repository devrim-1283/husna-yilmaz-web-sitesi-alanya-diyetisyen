    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>Hakkımda</h3>
                    <p><?php echo truncate($settings['about_text'] ?? 'Sağlıklı yaşam yolculuğunuzda yanınızdayım.', 120); ?></p>
                    <div class="footer-social">
                        <a href="https://wa.me/<?php echo clean($settings['whatsapp_number'] ?? '905536998982'); ?>" 
                           target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="<?php echo clean($settings['instagram_url'] ?? '#'); ?>" 
                           target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Hızlı Linkler</h3>
                    <ul class="footer-links">
                        <li><a href="/">Ana Sayfa</a></li>
                        <li><a href="/about">Hakkımda</a></li>
                        <li><a href="/services">Hizmetlerim</a></li>
                        <li><a href="/blog">Blog</a></li>
                        <li><a href="/contact">İletişim</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>İletişim</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Alanya, Antalya</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:+905536998982">+90 553 699 89 82</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:destek@husnayilmaz.com">destek@husnayilmaz.com</a>
                        </li>
                        <li style="display: block; margin-top: 10px;">
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <i class="far fa-clock"></i>
                                <strong style="margin-left: 8px;">Çalışma Saatleri</strong>
                            </div>
                            <div class="footer-working-hours">
                                <div><span>Pzt-Cuma:</span> <span>09:00 – 17:30</span></div>
                                <div><span>Cumartesi:</span> <span>09:00 – 14:00</span></div>
                                <div><span>Pazar:</span> <span style="color: #ff6b6b;">Kapalı</span></div>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Konum</h3>
                    <div class="footer-map">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3205.441130387499!2d32.000434!3d36.543475199999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14dc996cca53536d%3A0xafb7006971068a25!2zRGl5ZXRpc3llbiBIw7xzbmEgWcSxbG1heiBCZXNsZW5tZSBEYW7EscWfbWFubMSxayBNZXJrZXpp!5e0!3m2!1str!2str!4v1761087215309!5m2!1str!2str"
                            style="border:0;"
                            allowfullscreen="" 
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <a href="https://maps.app.goo.gl/A2TD94jUF7dr514F6" 
                       target="_blank" class="map-link" style="margin-top: 15px;">
                        <i class="fas fa-map-marked-alt"></i> Haritada Görüntüle
                    </a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Hüsna Yılmaz. Tüm hakları saklıdır.</p>
                <p class="developer-credit">
                    <a href="https://www.devrimsoft.com" target="_blank">www.devrimsoft.com</a> tarafından geliştirildi.
                </p>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="<?php echo assetUrl('assets/js/main.js'); ?>"></script>
</body>
</html>

