<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Günlük Yağ aracı ziyareti kaydet
trackPageView('tool_yag', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Yağ İhtiyacı Hesaplama';
$page_description = 'Hedefinize göre günlük yağ ihtiyacınızı gram cinsinden hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-oil-can"></i> Günlük Yağ İhtiyacı Hesaplama</h1>
        <p>Sağlıklı yağ tüketimi için günlük ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Yağ Hesaplayıcı</h2>
                
                <form id="yagForm">
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
                        Yağ İhtiyacını Hesapla
                    </button>
                </form>

                <div id="yagResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-oil-can"></i>
                    </div>
                    <h3>Günlük Yağ İhtiyacınız</h3>
                    <div class="result-value">
                        <span id="yagValue"></span>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 5px 0;"><strong>Toplam Kalori:</strong> <span id="toplamKalori"></span> kcal</p>
                        <p style="margin: 5px 0;"><strong>Yağ Oranı:</strong> <span id="yagOran"></span>%</p>
                        <p style="margin: 5px 0;"><strong>Yağ Kalorisi:</strong> <span id="yagKalori"></span> kcal</p>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="yagAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Yağ Neden Önemli?</h3>
                <p>Yağlar, hormon üretimi, vitamin emilimi, enerji depolama ve hücre zarı yapısı için gereklidir. Ancak doğru türde yağ seçmek çok önemlidir.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Formülü</h4>
                    <div class="formula-box">
                        Yağ (g) = [<span>Toplam Kalori</span> × <span>Yağ %</span>] ÷ <span>9</span>
                    </div>
                    <small style="display: block; margin-top: 10px; color: var(--text-dark);">
                        <i class="fas fa-info-circle"></i> 1 gram yağ = 9 kcal
                    </small>
                </div>

                <h4><i class="fas fa-chart-pie"></i> Hedefe Göre Yağ Oranı</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Kilo Vermek</span>
                        <span class="range">25%</span>
                    </div>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Kilo Koruma</span>
                        <span class="range">25%</span>
                    </div>
                    <div class="vki-table-row" style="background: #f1f8e9;">
                        <span class="category">Kilo Almak</span>
                        <span class="range">25%</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff3e0;">
                        <span class="category">Kas Yapmak</span>
                        <span class="range">20-25%</span>
                    </div>
                </div>

                <h4><i class="fas fa-leaf"></i> Yağ Türleri ve Kaynakları</h4>
                <table style="width: 100%; background: white; border-radius: 8px; overflow: hidden; margin-top: 15px;">
                    <thead style="background: var(--primary-green); color: white;">
                        <tr>
                            <th style="padding: 12px; text-align: left;">Yağ Türü</th>
                            <th style="padding: 12px; text-align: left;">Kaynak</th>
                            <th style="padding: 12px; text-align: center;">Öneri</th>
                        </tr>
                    </thead>
                    <tbody style="color: var(--text-dark);">
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Doymamış Yağlar</td>
                            <td style="padding: 12px;">Zeytinyağı, avokado, fındık, balık</td>
                            <td style="padding: 12px; text-align: center;">✅ Sağlıklı</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Doymuş Yağlar</td>
                            <td style="padding: 12px;">Tereyağı, kırmızı et, süt ürünleri</td>
                            <td style="padding: 12px; text-align: center;">⚠️ Sınırlı</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px;">Trans Yağlar</td>
                            <td style="padding: 12px;">Hazır gıdalar, margarin</td>
                            <td style="padding: 12px; text-align: center;">❌ Kaçının</td>
                        </tr>
                    </tbody>
                </table>

                <h4><i class="fas fa-lightbulb"></i> Öneriler</h4>
                <ul>
                    <li><strong>Omega-3:</strong> Balık, ceviz, keten tohumu tüketin</li>
                    <li><strong>Zeytinyağı:</strong> Ana yağ kaynağınız olsun</li>
                    <li><strong>Avokado:</strong> Sağlıklı yağ deposu</li>
                    <li><strong>Fındık/Badem:</strong> Günde 1 avuç tüketin</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Yağ türüne dikkat edin! Sağlıklı yağlar (omega-3, tek doymamış) tercih edin. Trans yağlardan kaçının.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('yagForm').addEventListener('submit', function(e) {
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
    
    // Hedefe göre kalori ve yağ oranı
    let hedefKalori, yagOran;
    
    switch(hedef) {
        case 'vermek':
            hedefKalori = tdee * 0.85;
            yagOran = 25;
            break;
        case 'koruma':
            hedefKalori = tdee * 1.0;
            yagOran = 25;
            break;
        case 'almak':
            hedefKalori = tdee * 1.15;
            yagOran = 25;
            break;
        case 'kas':
            hedefKalori = tdee * 1.1;
            yagOran = 22;
            break;
    }
    
    // Yağ hesaplama (1g yağ = 9 kcal)
    const yagKalori = (hedefKalori * yagOran) / 100;
    const yagGram = yagKalori / 9;
    
    // Sonuçları göster
    document.getElementById('toplamKalori').textContent = Math.round(hedefKalori);
    document.getElementById('yagOran').textContent = yagOran;
    document.getElementById('yagKalori').textContent = Math.round(yagKalori);
    document.getElementById('yagValue').textContent = Math.round(yagGram) + ' gram/gün';
    
    let hedefText = '';
    switch(hedef) {
        case 'vermek': hedefText = 'kilo vermek'; break;
        case 'koruma': hedefText = 'kilonuzu korumak'; break;
        case 'almak': hedefText = 'kilo almak'; break;
        case 'kas': hedefText = 'kas yapmak'; break;
    }
    
    const advice = `${hedefText} için günde ${Math.round(yagGram)} gram yağ tüketmelisiniz. Bu miktarın çoğunu zeytinyağı, avokado, fındık ve balık gibi sağlıklı kaynaklardan alın. Omega-3 açısından zengin balık tüketimi özellikle önemlidir. Trans yağlardan ve kızartmalardan uzak durun.`;
    
    document.getElementById('yagAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('yagResult');
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

