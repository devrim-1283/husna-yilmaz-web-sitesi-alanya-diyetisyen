<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Günlük Su aracı ziyareti kaydet
trackPageView('tool_su', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Su İhtiyacı Hesaplama';
$page_description = 'Vücut ağırlığınıza ve aktivite düzeyinize göre günlük su ihtiyacınızı hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-tint"></i> Günlük Su İhtiyacı Hesaplama</h1>
        <p>Sağlıklı yaşam için günlük su ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Su İhtiyacı Hesaplayıcı</h2>
                
                <form id="suForm">
                    <div class="form-group">
                        <label class="form-label" for="kilo">Kilo (kg) *</label>
                        <input type="number" id="kilo" name="kilo" class="form-control" placeholder="Kilonuzu girin" min="1" step="0.1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="aktivite">Aktivite Düzeyi *</label>
                        <select id="aktivite" name="aktivite" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="0">Masa Başı (Hareketsiz)</option>
                            <option value="0.3">Hafif Aktif (Günlük kısa yürüyüş)</option>
                            <option value="0.6">Orta Aktif (Haftada 3-5 gün egzersiz)</option>
                            <option value="1.0">Çok Aktif (Günlük spor)</option>
                            <option value="1.5">Aşırı Aktif (Yoğun fiziksel iş)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Su İhtiyacını Hesapla
                    </button>
                </form>

                <div id="suResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3>Günlük Su İhtiyacınız</h3>
                    <div class="result-value">
                        <span id="suValue"></span>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 5px 0;"><strong>Temel İhtiyaç:</strong> <span id="temelSu"></span> L</p>
                        <p style="margin: 5px 0;"><strong>Aktivite Eklentisi:</strong> +<span id="ekSu"></span> L</p>
                        <p style="margin: 5px 0;"><strong>Toplam (Bardak):</strong> <span id="suBardak"></span> bardak</p>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="suAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Su Neden Önemli?</h3>
                <p>Su, vücudun tüm temel işlevleri için gereklidir. Metabolizma, toksin atılımı, vücut ısısı düzenlenmesi ve enerji üretimi için su şarttır.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Formülü</h4>
                    <div class="formula-box">
                        <strong>Temel Su İhtiyacı:</strong><br>
                        Su (L) = <span>Kilo (kg)</span> × <span>0.033</span>
                    </div>
                    <div class="formula-box" style="margin-top: 15px;">
                        <strong>Toplam Su İhtiyacı:</strong><br>
                        Toplam = Temel İhtiyaç + <span>Aktivite Eklentisi</span>
                    </div>
                    <small style="display: block; margin-top: 10px; color: var(--text-dark);">
                        <i class="fas fa-info-circle"></i> Her 1 kg vücut ağırlığı için yaklaşık 33 mL su
                    </small>
                </div>

                <h4><i class="fas fa-running"></i> Aktiviteye Göre Ek Su</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Hareketsiz</span>
                        <span class="range">+0 L</span>
                    </div>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Hafif Aktif</span>
                        <span class="range">+0.3 L</span>
                    </div>
                    <div class="vki-table-row" style="background: #f1f8e9;">
                        <span class="category">Orta Aktif</span>
                        <span class="range">+0.6 L</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff3e0;">
                        <span class="category">Çok Aktif</span>
                        <span class="range">+1.0 L</span>
                    </div>
                    <div class="vki-table-row" style="background: #fce4ec;">
                        <span class="category">Aşırı Aktif</span>
                        <span class="range">+1.5 L</span>
                    </div>
                </div>

                <h4><i class="fas fa-glass-water"></i> Su Karşılıkları</h4>
                <ul>
                    <li><strong>1 Litre =</strong> 4 su bardağı (250 ml)</li>
                    <li><strong>2 Litre =</strong> 8 su bardağı</li>
                    <li><strong>2.5 Litre =</strong> 10 su bardağı</li>
                    <li><strong>3 Litre =</strong> 12 su bardağı</li>
                </ul>

                <h4><i class="fas fa-lightbulb"></i> Ek Düzeltme Faktörleri</h4>
                <ul>
                    <li><strong>Sıcak Hava / Terleme:</strong> +0.5–1.0 L</li>
                    <li><strong>Hamilelik / Emzirme:</strong> +0.3–0.7 L</li>
                    <li><strong>Kafein / Alkol:</strong> +0.25 L</li>
                    <li><strong>Yüksek Protein Diyeti:</strong> +0.25–0.5 L</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>İpucu:</strong> Suyu gün boyunca eşit aralıklarla tüketin. Örneğin saatte 1 bardak su içme alışkanlığı edinin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('suForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const kilo = parseFloat(document.getElementById('kilo').value);
    const aktiviteEk = parseFloat(document.getElementById('aktivite').value);
    
    if (!kilo || aktiviteEk === '') {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // Temel su ihtiyacı hesaplama (33 ml/kg)
    const temelSu = (kilo * 0.033).toFixed(2);
    
    // Toplam su ihtiyacı
    const toplamSu = (parseFloat(temelSu) + aktiviteEk).toFixed(1);
    
    // Bardak hesaplama (1 bardak = 250 ml)
    const bardakSayisi = Math.round((parseFloat(toplamSu) * 1000) / 250);
    
    // Sonuçları göster
    document.getElementById('temelSu').textContent = temelSu;
    document.getElementById('ekSu').textContent = aktiviteEk.toFixed(1);
    document.getElementById('suValue').textContent = toplamSu + ' Litre/gün';
    document.getElementById('suBardak').textContent = bardakSayisi;
    
    let aktiviteText = '';
    if (aktiviteEk === 0) aktiviteText = 'hareketsiz bir yaşam tarzı';
    else if (aktiviteEk === 0.3) aktiviteText = 'hafif aktif bir yaşam tarzı';
    else if (aktiviteEk === 0.6) aktiviteText = 'orta aktif bir yaşam tarzı';
    else if (aktiviteEk === 1.0) aktiviteText = 'çok aktif bir yaşam tarzı';
    else aktiviteText = 'aşırı aktif bir yaşam tarzı ve yoğun fiziksel iş';
    
    const advice = `${kilo} kg vücut ağırlığınız ve ${aktiviteText} için günde ${toplamSu} litre (yaklaşık ${bardakSayisi} bardak) su tüketmelisiniz. Suyu gün boyunca eşit aralıklarla içmeyi unutmayın. Sıcak havalarda, yoğun egzersiz sonrası veya yüksek protein diyeti sırasında su ihtiyacınız artabilir.`;
    
    document.getElementById('suAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('suResult');
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

