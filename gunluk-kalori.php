<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

trackPageView('tool_kalori', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Kalori İhtiyacı Hesaplama';
$page_description = 'Aktivite düzeyinize göre günlük toplam enerji ihtiyacınızı (TDEE) hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-fire"></i> Günlük Kalori İhtiyacı Hesaplama</h1>
        <p>Aktivite düzeyinize göre toplam enerji ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> TDEE Hesaplayıcı</h2>
                
                <form id="kaloriForm">
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

                    <div class="form-group">
                        <label class="form-label" for="aktivite">Aktivite Düzeyi *</label>
                        <select id="aktivite" name="aktivite" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="1.2">Hareketsiz (Masa başı, egzersiz yok)</option>
                            <option value="1.375">Az Aktif (Haftada 1-3 gün hafif egzersiz)</option>
                            <option value="1.55">Orta Aktif (Haftada 3-5 gün egzersiz)</option>
                            <option value="1.725">Çok Aktif (Günlük yoğun egzersiz)</option>
                            <option value="1.9">Aşırı Aktif (Ağır iş + sık egzersiz)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="hedef">Hedefiniz *</label>
                        <select id="hedef" name="hedef" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="0.85">Kilo Vermek (-15%)</option>
                            <option value="1.0">Kilo Koruma</option>
                            <option value="1.15">Kilo Almak (+15%)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Kalori İhtiyacını Hesapla
                    </button>
                </form>

                <div id="kaloriResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3>Günlük Kalori İhtiyacınız</h3>
                    <div class="result-value">
                        <span id="kaloriValue"></span>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 5px 0;"><strong>BMH (Dinlenme):</strong> <span id="bmhValue"></span> kcal</p>
                        <p style="margin: 5px 0;"><strong>TDEE (Toplam):</strong> <span id="tdeeValue"></span> kcal</p>
                        <p style="margin: 5px 0;"><strong>Hedefiniz için:</strong> <span id="hedefValue"></span> kcal</p>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="kaloriAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> TDEE Nedir?</h3>
                <p>TDEE (Total Daily Energy Expenditure), günlük toplam enerji harcamanızdır. BMH (Bazal Metabolizma Hızı) + aktivite düzeyinizle hesaplanır.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Adımları</h4>
                    <div class="formula-box">
                        <strong>1. Adım: BMH Hesapla</strong><br>
                        BMH = 10 × <span>kilo</span> + 6.25 × <span>boy</span> - 5 × <span>yaş</span> + <span>5 (erkek)</span> veya <span>-161 (kadın)</span>
                    </div>
                    <div class="formula-box" style="margin-top: 15px;">
                        <strong>2. Adım: TDEE Hesapla</strong><br>
                        TDEE = BMH × <span>Aktivite Çarpanı</span>
                    </div>
                    <div class="formula-box" style="margin-top: 15px;">
                        <strong>3. Adım: Hedefe Göre Ayarla</strong><br>
                        Hedef Kalori = TDEE × <span>Hedef Çarpanı</span>
                    </div>
                </div>

                <h4><i class="fas fa-chart-line"></i> Aktivite Çarpanları</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Hareketsiz</span>
                        <span class="range">× 1.2</span>
                    </div>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Az Aktif</span>
                        <span class="range">× 1.375</span>
                    </div>
                    <div class="vki-table-row" style="background: #f1f8e9;">
                        <span class="category">Orta Aktif</span>
                        <span class="range">× 1.55</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff3e0;">
                        <span class="category">Çok Aktif</span>
                        <span class="range">× 1.725</span>
                    </div>
                    <div class="vki-table-row" style="background: #fce4ec;">
                        <span class="category">Aşırı Aktif</span>
                        <span class="range">× 1.9</span>
                    </div>
                </div>

                <h4><i class="fas fa-lightbulb"></i> Hedef Çarpanları</h4>
                <ul>
                    <li><strong>Kilo Vermek:</strong> TDEE × 0.85 (-%15 kalori açığı)</li>
                    <li><strong>Kilo Koruma:</strong> TDEE × 1.0 (Mevcut kiloyu koru)</li>
                    <li><strong>Kilo Almak:</strong> TDEE × 1.15 (+%15 kalori fazlası)</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Sağlıklı kilo kaybı haftada 0.5-1 kg'dır. Aşırı kalori kısıtlaması metabolizmayı yavaşlatabilir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('kaloriForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const yas = parseFloat(document.getElementById('yas').value);
    const kilo = parseFloat(document.getElementById('kilo').value);
    const boy = parseFloat(document.getElementById('boy').value);
    const aktivite = parseFloat(document.getElementById('aktivite').value);
    const hedef = parseFloat(document.getElementById('hedef').value);
    
    if (!cinsiyet || !yas || !kilo || !boy || !aktivite || !hedef) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // BMH Hesaplama (Mifflin-St Jeor)
    let bmh;
    if (cinsiyet === 'erkek') {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) + 5;
    } else {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) - 161;
    }
    
    // TDEE Hesaplama
    const tdee = bmh * aktivite;
    
    // Hedefe Göre Kalori
    const hedefKalori = tdee * hedef;
    
    // Sonuçları göster
    document.getElementById('bmhValue').textContent = Math.round(bmh);
    document.getElementById('tdeeValue').textContent = Math.round(tdee);
    document.getElementById('hedefValue').textContent = Math.round(hedefKalori);
    document.getElementById('kaloriValue').textContent = Math.round(hedefKalori) + ' kcal/gün';
    
    let hedefText = '';
    if (hedef < 1.0) {
        hedefText = 'kilo vermek';
    } else if (hedef > 1.0) {
        hedefText = 'kilo almak';
    } else {
        hedefText = 'kilonuzu korumak';
    }
    
    let advice = `${hedefText} için günde ${Math.round(hedefKalori)} kalori tüketmelisiniz. `;
    advice += 'Bu kalori ihtiyacını protein, karbonhidrat ve yağ dengesi ile karşılamalısınız. ';
    advice += 'Makro besin dağılımınızı öğrenmek için "Günlük Makro Besin İhtiyacı" aracını kullanın.';
    
    document.getElementById('kaloriAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('kaloriResult');
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

