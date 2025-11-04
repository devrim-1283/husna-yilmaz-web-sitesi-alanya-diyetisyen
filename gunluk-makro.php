<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Günlük Makro aracı ziyareti kaydet
trackPageView('tool_makro', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Makro Besin İhtiyacı Hesaplama';
$page_description = 'Protein, karbonhidrat ve yağ ihtiyacınızı birlikte hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-chart-pie"></i> Günlük Makro Besin İhtiyacı</h1>
        <p>Protein, karbonhidrat ve yağ dengenizi öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Makro Hesaplayıcı</h2>
                
                <form id="makroForm">
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
                        Makro İhtiyacını Hesapla
                    </button>
                </form>

                <div id="makroResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3>Günlük Makro Besin İhtiyacınız</h3>
                    <div style="background: rgba(255,255,255,0.3); padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <p style="margin: 10px 0; font-size: 16px;"><strong>Toplam Kalori:</strong> <span id="toplamKalori"></span> kcal</p>
                        <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 15px 0;">
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p style="margin: 8px 0; font-size: 18px;"><i class="fas fa-drumstick-bite"></i> <strong>Protein:</strong> <span id="proteinValue"></span></p>
                            <small style="opacity: 0.9;"><span id="proteinOran"></span>% • <span id="proteinKcal"></span> kcal</small>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p style="margin: 8px 0; font-size: 18px;"><i class="fas fa-bread-slice"></i> <strong>Karbonhidrat:</strong> <span id="karboValue"></span></p>
                            <small style="opacity: 0.9;"><span id="karboOran"></span>% • <span id="karboKcal"></span> kcal</small>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p style="margin: 8px 0; font-size: 18px;"><i class="fas fa-oil-can"></i> <strong>Yağ:</strong> <span id="yagValue"></span></p>
                            <small style="opacity: 0.9;"><span id="yagOran"></span>% • <span id="yagKcal"></span> kcal</small>
                        </div>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="makroAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Makro Besinler Nedir?</h3>
                <p>Makro besinler (protein, karbonhidrat, yağ), vücudun enerji ihtiyacını karşılayan ve farklı işlevleri olan temel besin öğeleridir.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Formülleri</h4>
                    <div class="formula-box">
                        <strong>Protein (g):</strong> [Toplam Kalori × <span>Protein %</span>] ÷ <span>4</span>
                    </div>
                    <div class="formula-box" style="margin-top: 10px;">
                        <strong>Karbonhidrat (g):</strong> [Toplam Kalori × <span>Karbo %</span>] ÷ <span>4</span>
                    </div>
                    <div class="formula-box" style="margin-top: 10px;">
                        <strong>Yağ (g):</strong> [Toplam Kalori × <span>Yağ %</span>] ÷ <span>9</span>
                    </div>
                </div>

                <h4><i class="fas fa-balance-scale"></i> Hedefe Göre Makro Dağılımı</h4>
                <table style="width: 100%; background: white; border-radius: 8px; overflow: hidden; margin-top: 15px;">
                    <thead style="background: var(--primary-green); color: white;">
                        <tr>
                            <th style="padding: 12px; text-align: left;">Hedef</th>
                            <th style="padding: 12px; text-align: center;">Protein</th>
                            <th style="padding: 12px; text-align: center;">Karbo</th>
                            <th style="padding: 12px; text-align: center;">Yağ</th>
                        </tr>
                    </thead>
                    <tbody style="color: var(--text-dark);">
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Kilo Vermek</td>
                            <td style="padding: 12px; text-align: center;"><strong>30%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>45%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>25%</strong></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Kilo Koruma</td>
                            <td style="padding: 12px; text-align: center;"><strong>25%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>50%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>25%</strong></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Kilo Almak</td>
                            <td style="padding: 12px; text-align: center;"><strong>20%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>55%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>25%</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px;">Kas Yapmak</td>
                            <td style="padding: 12px; text-align: center;"><strong>30%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>50%</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>20%</strong></td>
                        </tr>
                    </tbody>
                </table>

                <h4><i class="fas fa-lightbulb"></i> Önemli Bilgiler</h4>
                <ul>
                    <li><strong>1 gram Protein =</strong> 4 kalori</li>
                    <li><strong>1 gram Karbonhidrat =</strong> 4 kalori</li>
                    <li><strong>1 gram Yağ =</strong> 9 kalori</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Makro dengesi kişisel hedefinize göre değişir. Bu değerleri 4-6 öğüne bölerek tüketin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('makroForm').addEventListener('submit', function(e) {
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
    
    // Hedefe göre kalori ve makro oranları
    let hedefKalori, proteinOran, karboOran, yagOran;
    
    switch(hedef) {
        case 'vermek':
            hedefKalori = tdee * 0.85;
            proteinOran = 30;
            karboOran = 45;
            yagOran = 25;
            break;
        case 'koruma':
            hedefKalori = tdee * 1.0;
            proteinOran = 25;
            karboOran = 50;
            yagOran = 25;
            break;
        case 'almak':
            hedefKalori = tdee * 1.15;
            proteinOran = 20;
            karboOran = 55;
            yagOran = 25;
            break;
        case 'kas':
            hedefKalori = tdee * 1.1;
            proteinOran = 30;
            karboOran = 50;
            yagOran = 20;
            break;
    }
    
    // Makro hesaplama
    const proteinKcal = (hedefKalori * proteinOran) / 100;
    const proteinGram = proteinKcal / 4;
    
    const karboKcal = (hedefKalori * karboOran) / 100;
    const karboGram = karboKcal / 4;
    
    const yagKcal = (hedefKalori * yagOran) / 100;
    const yagGram = yagKcal / 9;
    
    // Sonuçları göster
    document.getElementById('toplamKalori').textContent = Math.round(hedefKalori);
    
    document.getElementById('proteinValue').textContent = Math.round(proteinGram) + ' g/gün';
    document.getElementById('proteinOran').textContent = proteinOran;
    document.getElementById('proteinKcal').textContent = Math.round(proteinKcal);
    
    document.getElementById('karboValue').textContent = Math.round(karboGram) + ' g/gün';
    document.getElementById('karboOran').textContent = karboOran;
    document.getElementById('karboKcal').textContent = Math.round(karboKcal);
    
    document.getElementById('yagValue').textContent = Math.round(yagGram) + ' g/gün';
    document.getElementById('yagOran').textContent = yagOran;
    document.getElementById('yagKcal').textContent = Math.round(yagKcal);
    
    let hedefText = '';
    switch(hedef) {
        case 'vermek': hedefText = 'kilo vermek'; break;
        case 'koruma': hedefText = 'kilonuzu korumak'; break;
        case 'almak': hedefText = 'kilo almak'; break;
        case 'kas': hedefText = 'kas yapmak'; break;
    }
    
    const advice = `${hedefText} için günlük ${Math.round(proteinGram)}g protein, ${Math.round(karboGram)}g karbonhidrat ve ${Math.round(yagGram)}g yağ tüketmelisiniz. Bu dengeyi koruyarak hedefinize ulaşabilirsiniz.`;
    
    document.getElementById('makroAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('makroResult');
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

