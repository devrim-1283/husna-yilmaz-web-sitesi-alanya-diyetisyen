<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: İdeal Kilo aracı ziyareti kaydet
trackPageView('tool_ideal_kilo', $_SERVER['REQUEST_URI']);

$page_title = 'İdeal Kilo Hesaplama';
$page_description = 'BMI ve Devine formülleriyle sağlıklı kilo aralığınızı öğrenin';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-weight"></i> İdeal Kilo Hesaplama</h1>
        <p>Sağlıklı kilo aralığınızı ve ideal kilonuzu öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> İdeal Kilo Hesaplayıcı</h2>
                
                <form id="idealKiloForm">
                    <div class="form-group">
                        <label class="form-label" for="cinsiyet">Cinsiyet *</label>
                        <select id="cinsiyet" name="cinsiyet" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="erkek">Erkek</option>
                            <option value="kadin">Kadın</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="boy">Boy (cm) *</label>
                        <input type="number" id="boy" name="boy" class="form-control" placeholder="Boyunuzu girin" min="1" max="300" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mevcutKilo">Mevcut Kilonuz (kg) - Opsiyonel</label>
                        <input type="number" id="mevcutKilo" name="mevcutKilo" class="form-control" placeholder="Mevcut kilonuzu girin" min="1" step="0.1">
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Mevcut BMI'nizi görmek için</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        İdeal Kiloyu Hesapla
                    </button>
                </form>

                <div id="idealKiloResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-weight"></i>
                    </div>
                    <h3>İdeal Kilo Aralığınız</h3>
                    <div style="background: rgba(255,255,255,0.3); padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p style="margin: 8px 0; font-size: 18px;"><i class="fas fa-weight"></i> <strong>BMI Yöntemi:</strong></p>
                            <p style="margin: 8px 0; font-size: 16px;"><span id="bmiAralik"></span></p>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p style="margin: 8px 0; font-size: 18px;"><i class="fas fa-stethoscope"></i> <strong>Devine Formülü:</strong></p>
                            <p style="margin: 8px 0; font-size: 16px;"><span id="devineKilo"></span></p>
                        </div>
                        <div id="mevcutDurum" style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin: 10px 0; display: none;">
                            <p style="margin: 8px 0; font-size: 16px;"><strong>Mevcut Kilonuz:</strong> <span id="mevcutKiloText"></span></p>
                            <p style="margin: 8px 0; font-size: 16px;"><strong>Mevcut BMI:</strong> <span id="mevcutBMI"></span></p>
                            <p style="margin: 8px 0; font-size: 16px;"><strong>Durum:</strong> <span id="durumText"></span></p>
                        </div>
                    </div>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="idealKiloAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> İdeal Kilo Nedir?</h3>
                <p>İdeal kilo, boy ve cinsiyetinize göre sağlıklı kabul edilen kilo aralığıdır. İki farklı yöntemle hesaplanabilir.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> BMI (Vücut Kitle İndeksi) Yöntemi</h4>
                    <div class="formula-box">
                        <strong>Alt Sınır:</strong> 18.5 × <span>(Boy(m))²</span><br>
                        <strong>Üst Sınır:</strong> 24.9 × <span>(Boy(m))²</span>
                    </div>
                </div>

                <div class="vki-formula" style="margin-top: 20px;">
                    <h4><i class="fas fa-flask"></i> Devine Formülü</h4>
                    <div class="formula-box">
                        <strong>Erkek:</strong> 50 + 2.3 × <span>(Boy(inç) - 60)</span>
                    </div>
                    <div class="formula-box" style="margin-top: 10px;">
                        <strong>Kadın:</strong> 45.5 + 2.3 × <span>(Boy(inç) - 60)</span>
                    </div>
                    <small style="display: block; margin-top: 10px; color: var(--text-dark);">
                        <i class="fas fa-info-circle"></i> 1 inç = 2.54 cm
                    </small>
                </div>

                <h4><i class="fas fa-table"></i> BMI Kategorileri</h4>
                <div class="vki-table">
                    <div class="vki-table-row" style="background: #e3f2fd;">
                        <span class="category">Zayıf</span>
                        <span class="range">< 18.5</span>
                    </div>
                    <div class="vki-table-row" style="background: #c8e6c9;">
                        <span class="category">Normal (Sağlıklı) ✅</span>
                        <span class="range">18.5 - 24.9</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff9c4;">
                        <span class="category">Fazla Kilolu</span>
                        <span class="range">25 - 29.9</span>
                    </div>
                    <div class="vki-table-row" style="background: #ffccbc;">
                        <span class="category">Obez (1. Derece)</span>
                        <span class="range">30 - 34.9</span>
                    </div>
                    <div class="vki-table-row" style="background: #ef9a9a;">
                        <span class="category">Obez (2. Derece)</span>
                        <span class="range">35 - 39.9</span>
                    </div>
                    <div class="vki-table-row" style="background: #e57373;">
                        <span class="category">Morbid Obez ⚠️</span>
                        <span class="range">≥ 40</span>
                    </div>
                </div>

                <h4><i class="fas fa-lightbulb"></i> Önemli Bilgiler</h4>
                <ul>
                    <li><strong>Kas Oranı:</strong> Yüksek kas oranı BMI'yi artırabilir ama sağlık açısından sorun olmayabilir</li>
                    <li><strong>Yağ Dağılımı:</strong> Bel/kalça oranı da değerlendirilmelidir</li>
                    <li><strong>Yaşam Tarzı:</strong> Genetik yapı ve fiziksel aktivite de önemlidir</li>
                    <li><strong>Hedef Belirleme:</strong> Makul ve sürdürülebilir hedefler koyun</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> İdeal kilo sadece bir rehberdir. Kişisel hedefinizi belirlerken yaşam tarzınızı, genetik yapınızı ve sağlık durumunuzu da göz önünde bulundurun.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('idealKiloForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const boy = parseFloat(document.getElementById('boy').value);
    const mevcutKilo = parseFloat(document.getElementById('mevcutKilo').value) || 0;
    
    if (!cinsiyet || !boy) {
        alert('Lütfen cinsiyet ve boy bilgilerini girin!');
        return;
    }
    
    const boyMetre = boy / 100;
    
    // BMI Yöntemiyle İdeal Kilo Aralığı
    const idealKiloAlt = 18.5 * (boyMetre * boyMetre);
    const idealKiloUst = 24.9 * (boyMetre * boyMetre);
    
    // Devine Formülü
    const boyInc = boy / 2.54;
    const farklilık = boyInc - 60;
    let devineIdeal;
    
    if (cinsiyet === 'erkek') {
        devineIdeal = 50 + (2.3 * farklilık);
    } else {
        devineIdeal = 45.5 + (2.3 * farklilık);
    }
    
    // Sonuçları göster
    document.getElementById('bmiAralik').textContent = `${Math.round(idealKiloAlt)} - ${Math.round(idealKiloUst)} kg`;
    document.getElementById('devineKilo').textContent = `≈ ${Math.round(devineIdeal)} kg`;
    
    // Mevcut kilo değerlendirmesi
    let advice = `Boyunuz ${boy} cm için sağlıklı kilo aralığı ${Math.round(idealKiloAlt)}-${Math.round(idealKiloUst)} kg'dır. `;
    advice += `Devine formülüne göre ideal kilonuz yaklaşık ${Math.round(devineIdeal)} kg'dır. `;
    
    if (mevcutKilo > 0) {
        const mevcutBMI = mevcutKilo / (boyMetre * boyMetre);
        
        document.getElementById('mevcutKiloText').textContent = mevcutKilo + ' kg';
        document.getElementById('mevcutBMI').textContent = mevcutBMI.toFixed(1);
        
        let durum, durumRenk;
        if (mevcutBMI < 18.5) {
            durum = 'Zayıf';
            durumRenk = '#2196F3';
            advice += `Mevcut kilonuz (${mevcutKilo} kg) ideal aralığın altında. Sağlıklı kilo almak için bir diyetisyene danışın.`;
        } else if (mevcutBMI <= 24.9) {
            durum = 'Normal (Sağlıklı Aralıkta) ✅';
            durumRenk = '#4CAF50';
            advice += `Harika! Mevcut kilonuz (${mevcutKilo} kg) sağlıklı aralıkta. Bu kiloyu korumaya devam edin.`;
        } else if (mevcutBMI <= 29.9) {
            durum = 'Fazla Kilolu';
            durumRenk = '#FF9800';
            advice += `Mevcut kilonuz (${mevcutKilo} kg) ideal aralığın üstünde. Dengeli beslenme ve egzersiz ile sağlıklı kilo verin.`;
        } else if (mevcutBMI <= 34.9) {
            durum = 'Obez (1. Derece)';
            durumRenk = '#F44336';
            advice += `Dikkat! Mevcut kilonuz (${mevcutKilo} kg) obezite düzeyinde. Mutlaka bir diyetisyen ve doktora danışın.`;
        } else if (mevcutBMI <= 39.9) {
            durum = 'Obez (2. Derece)';
            durumRenk = '#D32F2F';
            advice += `Ciddi uyarı! Mevcut kilonuz (${mevcutKilo} kg) ciddi obezite düzeyinde. Acilen sağlık profesyoneline danışın.`;
        } else {
            durum = 'Morbid Obez ⚠️';
            durumRenk = '#B71C1C';
            advice += `Çok ciddi! Mevcut kilonuz (${mevcutKilo} kg) morbid obezite düzeyinde. Acil tıbbi destek alın.`;
        }
        
        document.getElementById('durumText').innerHTML = `<span style="color: ${durumRenk}; font-weight: 700;">${durum}</span>`;
        document.getElementById('mevcutDurum').style.display = 'block';
    } else {
        document.getElementById('mevcutDurum').style.display = 'none';
        advice += 'İdeal kiloya ulaşmak için düzenli egzersiz ve dengeli beslenme önemlidir.';
    }
    
    document.getElementById('idealKiloAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('idealKiloResult');
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

