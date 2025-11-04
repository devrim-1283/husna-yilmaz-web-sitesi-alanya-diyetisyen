<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: BMH aracı ziyareti kaydet
trackPageView('tool_bmh', $_SERVER['REQUEST_URI']);

$page_title = 'Bazal Metabolizma Hızı (BMH) Hesaplama';
$page_description = 'Dinlenme hâlindeki enerji harcamanızı Mifflin-St Jeor formülüyle hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-heartbeat"></i> Bazal Metabolizma Hızı (BMH) Hesaplama</h1>
        <p>Dinlenme hâlindeki günlük enerji ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> BMH Hesaplayıcı</h2>
                
                <form id="bmhForm">
                    <div class="form-group">
                        <label class="form-label" for="cinsiyet">Cinsiyet *</label>
                        <select id="cinsiyet" name="cinsiyet" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="erkek">Erkek</option>
                            <option value="kadin">Kadın</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="yas">Yaş *</label>
                        <input type="number" id="yas" name="yas" class="form-control" placeholder="Yaşınızı girin" min="1" max="120" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="kilo">Kilo (kg) *</label>
                        <input type="number" id="kilo" name="kilo" class="form-control" placeholder="Kilonuzu girin" min="1" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="boy">Boy (cm) *</label>
                        <input type="number" id="boy" name="boy" class="form-control" placeholder="Boyunuzu girin" min="1" max="300" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        BMH Hesapla
                    </button>
                </form>

                <div id="bmhResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3>Sonuç</h3>
                    <div class="result-value">
                        <span id="bmhValue"></span>
                    </div>
                    <p id="bmhCategory"></p>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="bmhAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> BMH Nedir?</h3>
                <p>Bazal Metabolizma Hızı (BMH), vücudunuzun dinlenme hâlinde temel yaşam fonksiyonlarını sürdürmek için harcadığı minimum enerji miktarıdır.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Mifflin-St Jeor Formülü</h4>
                    <div class="formula-box">
                        <strong>Erkekler için:</strong><br>
                        BMH = 10 × <span>kilo(kg)</span> + 6.25 × <span>boy(cm)</span> - 5 × <span>yaş</span> + <span>5</span>
                    </div>
                    <div class="formula-box" style="margin-top: 15px;">
                        <strong>Kadınlar için:</strong><br>
                        BMH = 10 × <span>kilo(kg)</span> + 6.25 × <span>boy(cm)</span> - 5 × <span>yaş</span> - <span>161</span>
                    </div>
                </div>

                <h4><i class="fas fa-chart-line"></i> Örnek Hesaplama</h4>
                <p><strong>Kadın, 32 yaş, 69 kg, 172 cm:</strong></p>
                <ul style="list-style: none; padding-left: 0;">
                    <li>✓ 10 × 69 = 690</li>
                    <li>✓ 6.25 × 172 = 1075</li>
                    <li>✓ 5 × 32 = 160</li>
                    <li>✓ BMH = 690 + 1075 - 160 - 161 = <strong>1444 kcal/gün</strong></li>
                </ul>

                <h4><i class="fas fa-lightbulb"></i> BMH'yi Etkileyen Faktörler</h4>
                <ul>
                    <li><strong>Yaş:</strong> Yaş arttıkça metabolizma yavaşlar</li>
                    <li><strong>Cinsiyet:</strong> Erkeklerde genelde daha yüksektir</li>
                    <li><strong>Kas kütlesi:</strong> Kas dokusu daha fazla enerji harcar</li>
                    <li><strong>Genetik:</strong> Ailevi faktörler etkilidir</li>
                    <li><strong>Hormonlar:</strong> Tiroid hormonları metabolizmayı etkiler</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Not:</strong> BMH sadece dinlenme hâlindeki enerjiyi gösterir. Günlük toplam kalori ihtiyacınız için "Günlük Kalori İhtiyacı" aracını kullanın.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('bmhForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const yas = parseFloat(document.getElementById('yas').value);
    const kilo = parseFloat(document.getElementById('kilo').value);
    const boy = parseFloat(document.getElementById('boy').value);
    
    if (!cinsiyet || !yas || !kilo || !boy) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // Mifflin-St Jeor Formülü
    let bmh;
    if (cinsiyet === 'erkek') {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) + 5;
    } else {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) - 161;
    }
    
    bmh = Math.round(bmh);
    
    // Sonucu göster
    document.getElementById('bmhValue').textContent = bmh + ' kcal/gün';
    document.getElementById('bmhCategory').innerHTML = `<strong>Dinlenme hâlinde günlük enerji harcamanız</strong>`;
    
    let advice = `Vücudunuz temel yaşam fonksiyonları için günde ${bmh} kalori harcıyor. `;
    
    if (cinsiyet === 'erkek') {
        advice += 'Erkeklerde ortalama BMH 1600-1800 kcal arasındadır. ';
    } else {
        advice += 'Kadınlarda ortalama BMH 1200-1500 kcal arasındadır. ';
    }
    
    advice += 'Aktivite düzeyinize göre toplam kalori ihtiyacınız bu değerin 1.2-1.9 katı olabilir.';
    
    document.getElementById('bmhAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('bmhResult');
    resultDiv.style.display = 'block';
    resultDiv.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    
    setTimeout(() => {
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
});
</script>

<!-- CTA Randevu Section -->
<section class="section bg-cream">
    <div class="container">
        <div class="cta-card fade-in-up">
            <div class="cta-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="cta-content">
                <h2>Şimdi Randevu Oluşturun %40 İndirim!</h2>
                <p>Profesyonel diyet danışmanlığı ile hedeflerinize ulaşın. Kişiye özel beslenme programları için hemen randevu alın.</p>
            </div>
            <div class="cta-action">
                <a href="/contact#randevu" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Randevu Oluştur
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

