<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Günlük Karbonhidrat aracı ziyareti kaydet
trackPageView('tool_karbonhidrat', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Karbonhidrat İhtiyacı Hesaplama';
$page_description = 'Hedefinize göre günlük karbonhidrat ihtiyacınızı gram cinsinden hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-bread-slice"></i> Günlük Karbonhidrat İhtiyacı</h1>
        <p>Hedefinize özel karbonhidrat gram ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Karbonhidrat Hesaplayıcı</h2>
                
                <form id="karboForm">
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
                            <option value="1.2">Hareketsiz</option>
                            <option value="1.375">Az Aktif</option>
                            <option value="1.55">Orta Aktif</option>
                            <option value="1.725">Çok Aktif</option>
                            <option value="1.9">Aşırı Aktif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="hedef">Hedefiniz *</label>
                        <select id="hedef" name="hedef" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="vermek">Kilo Vermek</option>
                            <option value="koruma">Kilo Koruma</option>
                            <option value="almak">Kilo Almak</option>
                            <option value="kas">Kas Yapmak</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Karbonhidrat İhtiyacını Hesapla
                    </button>
                </form>

                <div id="karboResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-bread-slice"></i>
                    </div>
                    <h3>Günlük Karbonhidrat İhtiyacınız</h3>
                    <div class="result-value">
                        <span id="karboValue"></span>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 5px 0;"><strong>Toplam Kalori:</strong> <span id="toplamKalori"></span> kcal</p>
                        <p style="margin: 5px 0;"><strong>Karbonhidrat Oranı:</strong> <span id="karboOran"></span>%</p>
                        <p style="margin: 5px 0;"><strong>Karbonhidrat Kalorisi:</strong> <span id="karboKalori"></span> kcal</p>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="karboAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Karbonhidrat İhtiyacı Nedir?</h3>
                <p>Karbonhidratlar vücudun birincil enerji kaynağıdır. Hedefinize göre toplam kalorinin belirli bir yüzdesini karbonhidrattan almalısınız.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Formülü</h4>
                    <div class="formula-box">
                        Karbonhidrat (g) = [<span>Toplam Kalori</span> × <span>Karbonhidrat %</span>] ÷ <span>4</span>
                    </div>
                    <small style="display: block; margin-top: 10px; color: var(--text-dark);">
                        <i class="fas fa-info-circle"></i> 1 gram karbonhidrat = 4 kcal
                    </small>
                </div>

                <h4><i class="fas fa-chart-pie"></i> Hedefe Göre Karbonhidrat Oranı</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Kilo Vermek</span>
                        <span class="range">40-45%</span>
                    </div>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Kilo Koruma</span>
                        <span class="range">50%</span>
                    </div>
                    <div class="vki-table-row" style="background: #f1f8e9;">
                        <span class="category">Kilo Almak</span>
                        <span class="range">55-60%</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff3e0;">
                        <span class="category">Kas Yapmak</span>
                        <span class="range">50-55%</span>
                    </div>
                </div>

                <h4><i class="fas fa-apple-alt"></i> Karbonhidrat Kaynakları</h4>
                <ul>
                    <li><strong>Kompleks Karbonhidratlar (Tercih Edin):</strong> Tam tahıl ekmek, yulaf, esmer pirinç, kinoa, patates</li>
                    <li><strong>Basit Karbonhidratlar (Sınırlı):</strong> Beyaz ekmek, şeker, tatlılar, meyve suları</li>
                    <li><strong>Lifli Karbonhidratlar:</strong> Sebzeler, baklagiller, meyveler</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Karbonhidrat kaynağı olarak kompleks karbonhidratları tercih edin. Kan şekerinizi dengeli tutar ve uzun süre tok hissettirir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('karboForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const yas = parseFloat(document.getElementById('yas').value);
    const kilo = parseFloat(document.getElementById('kilo').value);
    const boy = parseFloat(document.getElementById('boy').value);
    const aktivite = parseFloat(document.getElementById('aktivite').value);
    const hedef = document.getElementById('hedef').value;
    
    if (!cinsiyet || !yas || !kilo || !boy || !aktivite || !hedef) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // BMH Hesaplama
    let bmh;
    if (cinsiyet === 'erkek') {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) + 5;
    } else {
        bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) - 161;
    }
    
    // TDEE Hesaplama
    const tdee = bmh * aktivite;
    
    // Hedefe göre kalori ve karbonhidrat oranı
    let hedefKalori, karboOran;
    
    switch(hedef) {
        case 'vermek':
            hedefKalori = tdee * 0.85;
            karboOran = 45;
            break;
        case 'koruma':
            hedefKalori = tdee * 1.0;
            karboOran = 50;
            break;
        case 'almak':
            hedefKalori = tdee * 1.15;
            karboOran = 55;
            break;
        case 'kas':
            hedefKalori = tdee * 1.1;
            karboOran = 52;
            break;
    }
    
    // Karbonhidrat hesaplama (1g karbo = 4 kcal)
    const karboKalori = (hedefKalori * karboOran) / 100;
    const karboGram = karboKalori / 4;
    
    // Sonuçları göster
    document.getElementById('toplamKalori').textContent = Math.round(hedefKalori);
    document.getElementById('karboOran').textContent = karboOran;
    document.getElementById('karboKalori').textContent = Math.round(karboKalori);
    document.getElementById('karboValue').textContent = Math.round(karboGram) + ' gram/gün';
    
    let hedefText = '';
    switch(hedef) {
        case 'vermek': hedefText = 'kilo vermek'; break;
        case 'koruma': hedefText = 'kilonuzu korumak'; break;
        case 'almak': hedefText = 'kilo almak'; break;
        case 'kas': hedefText = 'kas yapmak'; break;
    }
    
    let advice = `${hedefText} için günde ${Math.round(karboGram)} gram karbonhidrat tüketmelisiniz. `;
    advice += 'Bu miktarı tam tahıllar, sebzeler, meyveler ve baklagiller gibi sağlıklı kaynaklardan alın. ';
    advice += 'Protein ve yağ ihtiyacınızı da dengeli tutmayı unutmayın.';
    
    document.getElementById('karboAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('karboResult');
    resultDiv.style.display = 'block';
    resultDiv.style.background = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
    
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

