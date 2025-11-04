<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Günlük Protein aracı ziyareti kaydet
trackPageView('tool_protein', $_SERVER['REQUEST_URI']);

$page_title = 'Günlük Protein İhtiyacı Hesaplama';
$page_description = 'Vücut ağırlığınıza ve hedefinize göre günlük protein ihtiyacınızı hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-drumstick-bite"></i> Günlük Protein İhtiyacı</h1>
        <p>Hedefinize özel protein ihtiyacınızı öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Protein Hesaplayıcı</h2>
                
                <form id="proteinForm">
                    <div class="form-group">
                        <label class="form-label" for="kilo">Kilo (kg) *</label>
                        <input type="number" id="kilo" name="kilo" class="form-control" placeholder="Kilonuzu girin" min="1" step="0.1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="aktivite">Aktivite Düzeyi *</label>
                        <select id="aktivite" name="aktivite" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="sedanter">Hareketsiz (Masa başı)</option>
                            <option value="orta">Orta Aktif (Haftada 3-5 gün egzersiz)</option>
                            <option value="aktif">Çok Aktif / Sporcu</option>
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
                        Protein İhtiyacını Hesapla
                    </button>
                </form>

                <div id="proteinResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-drumstick-bite"></i>
                    </div>
                    <h3>Günlük Protein İhtiyacınız</h3>
                    <div class="result-value">
                        <span id="proteinValue"></span>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <p style="margin: 5px 0;"><strong>Protein Katsayısı:</strong> <span id="proteinKatsayi"></span> g/kg</p>
                        <p style="margin: 5px 0;"><strong>Hesaplama:</strong> <span id="proteinHesap"></span></p>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="proteinAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Protein Neden Önemli?</h3>
                <p>Protein, kas kütlesi koruma ve onarımı, metabolizma hızı ve tokluk hissi açısından en kritik makro besindir. Yetersiz protein, kas kaybına ve düşük enerjiye yol açar.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Hesaplama Formülü</h4>
                    <div class="formula-box">
                        Protein (g/gün) = <span>Kilo (kg)</span> × <span>Protein Katsayısı (g/kg)</span>
                    </div>
                </div>

                <h4><i class="fas fa-balance-scale"></i> Protein Katsayıları</h4>
                <table style="width: 100%; background: white; border-radius: 8px; overflow: hidden; margin-top: 15px;">
                    <thead style="background: var(--primary-green); color: white;">
                        <tr>
                            <th style="padding: 12px; text-align: left;">Hedef / Aktivite</th>
                            <th style="padding: 12px; text-align: center;">Sedanter</th>
                            <th style="padding: 12px; text-align: center;">Orta Aktif</th>
                            <th style="padding: 12px; text-align: center;">Çok Aktif</th>
                        </tr>
                    </thead>
                    <tbody style="color: var(--text-dark);">
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Kilo Vermek</td>
                            <td style="padding: 12px; text-align: center;"><strong>1.8</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>2.0</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>2.2</strong></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Koruma</td>
                            <td style="padding: 12px; text-align: center;"><strong>1.4</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>1.6</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>1.8</strong></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">Kilo Almak</td>
                            <td style="padding: 12px; text-align: center;"><strong>1.6</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>1.8</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>2.0</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px;">Kas Yapmak</td>
                            <td style="padding: 12px; text-align: center;"><strong>2.0</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>2.2</strong></td>
                            <td style="padding: 12px; text-align: center;"><strong>2.4</strong></td>
                        </tr>
                    </tbody>
                </table>

                <h4><i class="fas fa-apple-alt"></i> Protein Kaynakları (100g başına)</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Tavuk Göğsü</span>
                        <span class="range">31g</span>
                    </div>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Kırmızı Et</span>
                        <span class="range">26g</span>
                    </div>
                    <div class="vki-table-row" style="background: #f1f8e9;">
                        <span class="category">Balık</span>
                        <span class="range">22g</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff3e0;">
                        <span class="category">Yumurta (1 adet)</span>
                        <span class="range">6g</span>
                    </div>
                    <div class="vki-table-row" style="background: #fce4ec;">
                        <span class="category">Mercimek</span>
                        <span class="range">9g</span>
                    </div>
                </div>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Protein alımını gün boyunca 4-6 öğüne yayarak tüketin. Tek seferde çok fazla protein sindirim zorluğu yaratabilir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('proteinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const kilo = parseFloat(document.getElementById('kilo').value);
    const aktivite = document.getElementById('aktivite').value;
    const hedef = document.getElementById('hedef').value;
    
    if (!kilo || !aktivite || !hedef) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // Protein katsayısını belirle
    let katsayi;
    
    if (hedef === 'vermek') {
        if (aktivite === 'sedanter') katsayi = 1.8;
        else if (aktivite === 'orta') katsayi = 2.0;
        else katsayi = 2.2;
    } else if (hedef === 'koruma') {
        if (aktivite === 'sedanter') katsayi = 1.4;
        else if (aktivite === 'orta') katsayi = 1.6;
        else katsayi = 1.8;
    } else if (hedef === 'almak') {
        if (aktivite === 'sedanter') katsayi = 1.6;
        else if (aktivite === 'orta') katsayi = 1.8;
        else katsayi = 2.0;
    } else { // kas
        if (aktivite === 'sedanter') katsayi = 2.0;
        else if (aktivite === 'orta') katsayi = 2.2;
        else katsayi = 2.4;
    }
    
    // Protein hesaplama
    const proteinGram = kilo * katsayi;
    
    // Sonuçları göster
    document.getElementById('proteinValue').textContent = Math.round(proteinGram) + ' gram/gün';
    document.getElementById('proteinKatsayi').textContent = katsayi;
    document.getElementById('proteinHesap').textContent = `${kilo} kg × ${katsayi} = ${Math.round(proteinGram)} g`;
    
    let hedefText = '';
    switch(hedef) {
        case 'vermek': hedefText = 'kilo verirken kas kaybını önlemek'; break;
        case 'koruma': hedefText = 'kilonuzu korumak'; break;
        case 'almak': hedefText = 'sağlıklı kilo almak'; break;
        case 'kas': hedefText = 'kas yapmak'; break;
    }
    
    let aktiviteText = '';
    switch(aktivite) {
        case 'sedanter': aktiviteText = 'hareketsiz bir yaşam tarzı'; break;
        case 'orta': aktiviteText = 'orta düzeyde aktif bir yaşam'; break;
        case 'aktif': aktiviteText = 'çok aktif bir yaşam tarzı ve düzenli antrenman'; break;
    }
    
    const advice = `${hedefText} ve ${aktiviteText} için günde ${Math.round(proteinGram)} gram protein tüketmelisiniz. Bu miktarı tavuk, et, balık, yumurta, süt ürünleri ve baklagiller gibi kaynaklardan dengeli şekilde alın.`;
    
    document.getElementById('proteinAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('proteinResult');
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

