<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Bel-Kalça Oranı aracı ziyareti kaydet
trackPageView('tool_bel_kalca', $_SERVER['REQUEST_URI']);

$page_title = 'Bel / Kalça Oranı Hesaplama';
$page_description = 'Vücuttaki yağ dağılımını ve kardiyometabolik riskinizi değerlendirin';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-ruler"></i> Bel / Kalça Oranı Hesaplama</h1>
        <p>Yağ dağılımınızı ve sağlık riskinizi öğrenin</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Bel/Kalça Oranı Hesaplayıcı</h2>
                
                <form id="bkForm">
                    <div class="form-group">
                        <label class="form-label" for="cinsiyet">Cinsiyet *</label>
                        <select id="cinsiyet" name="cinsiyet" class="form-control" required>
                            <option value="">Seçiniz</option>
                            <option value="erkek">Erkek</option>
                            <option value="kadin">Kadın</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="bel">Bel Çevresi (cm) *</label>
                        <input type="number" id="bel" name="bel" class="form-control" placeholder="Bel çevrenizi girin" min="1" step="0.1" required>
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Göbek deliği hizasından ölçün</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="kalca">Kalça Çevresi (cm) *</label>
                        <input type="number" id="kalca" name="kalca" class="form-control" placeholder="Kalça çevrenizi girin" min="1" step="0.1" required>
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Kalçanın en geniş yerinden ölçün</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Oranı Hesapla
                    </button>
                </form>

                <div id="bkResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-ruler"></i>
                    </div>
                    <h3>Bel/Kalça Oranınız</h3>
                    <div class="result-value">
                        <span id="bkValue"></span>
                    </div>
                    <p id="bkCategory"></p>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="bkAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Bel/Kalça Oranı Nedir?</h3>
                <p>Bel/Kalça oranı (BKÖ), vücuttaki yağ dağılımını gösteren önemli bir sağlık göstergesidir. Özellikle karın bölgesindeki yağlanma, kardiyometabolik hastalıklar için risk faktörüdür.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> Formül</h4>
                    <div class="formula-box">
                        BKÖ = <span>Bel Çevresi (cm)</span> ÷ <span>Kalça Çevresi (cm)</span>
                    </div>
                </div>

                <h4><i class="fas fa-chart-line"></i> Değerlendirme Tablosu</h4>
                
                <div class="vki-table">
                    <h5 style="color: var(--primary-green); margin-bottom: 10px;">👩 Kadınlar İçin</h5>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Düşük Risk</span>
                        <span class="range">< 0.80</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff9c4;">
                        <span class="category">Orta Risk</span>
                        <span class="range">0.81 - 0.85</span>
                    </div>
                    <div class="vki-table-row" style="background: #ffccbc;">
                        <span class="category">Yüksek Risk</span>
                        <span class="range">> 0.85</span>
                    </div>
                </div>

                <div class="vki-table" style="margin-top: 20px;">
                    <h5 style="color: var(--primary-green); margin-bottom: 10px;">👨 Erkekler İçin</h5>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Düşük Risk</span>
                        <span class="range">< 0.90</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff9c4;">
                        <span class="category">Orta Risk</span>
                        <span class="range">0.90 - 0.95</span>
                    </div>
                    <div class="vki-table-row" style="background: #ffccbc;">
                        <span class="category">Yüksek Risk</span>
                        <span class="range">> 0.95</span>
                    </div>
                </div>

                <h4><i class="fas fa-lightbulb"></i> Ölçüm İpuçları</h4>
                <ul>
                    <li><strong>Bel:</strong> Göbek deliği hizasından, nefes verirken ölçün</li>
                    <li><strong>Kalça:</strong> Kalçanın en geniş kısmından ölçün</li>
                    <li><strong>Mezura:</strong> Sıkmadan, düz olarak tutun</li>
                    <li><strong>Zaman:</strong> Sabah aç karnına ölçüm yapın</li>
                </ul>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Önemli:</strong> Yüksek BKÖ, visseral (iç organ) yağlanması, tip 2 diyabet, kalp hastalığı ve hipertansiyon riskini artırır.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('bkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const bel = parseFloat(document.getElementById('bel').value);
    const kalca = parseFloat(document.getElementById('kalca').value);
    
    if (!cinsiyet || !bel || !kalca) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    // Bel/Kalça Oranı Hesaplama
    const bko = (bel / kalca).toFixed(2);
    
    // Risk değerlendirmesi
    let category, risk, advice, bgColor;
    
    if (cinsiyet === 'kadin') {
        if (bko < 0.80) {
            category = 'Düşük Risk';
            risk = 'Yağ dağılımınız sağlıklı görünüyor';
            advice = 'Mükemmel! Yağ dağılımınız dengede. Bu oranı korumaya devam edin. Düzenli egzersiz ve dengeli beslenme önemli.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (bko <= 0.85) {
            category = 'Orta Risk';
            risk = 'Yağ dağılımınız dengede';
            advice = 'Orta seviyede risk var. Karın bölgesindeki yağlanmayı azaltmak için düzenli egzersiz ve kalori kontrolü önemli.';
            bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
        } else {
            category = 'Yüksek Risk';
            risk = 'Karın bölgesinde yağlanma mevcut';
            advice = 'Dikkat! Karın bölgesindeki yağlanma, kalp-damar hastalıkları ve metabolik sendrom riskini artırır. Mutlaka beslenme uzmanına danışın ve düzenli egzersiz programı başlatın.';
            bgColor = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
        }
    } else { // erkek
        if (bko < 0.90) {
            category = 'Düşük Risk';
            risk = 'Yağ dağılımınız sağlıklı görünüyor';
            advice = 'Harika! Yağ dağılımınız sağlıklı aralıkta. Bu oranı korumak için aktif yaşam tarzınızı sürdürün.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (bko <= 0.95) {
            category = 'Orta Risk';
            risk = 'Bel çevresi artmaya başlamış';
            advice = 'Orta düzeyde risk var. Karın bölgesindeki yağlanmayı önlemek için diyetinize ve egzersiz rutininize dikkat edin.';
            bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
        } else {
            category = 'Yüksek Risk';
            risk = 'Karın bölgesinde yağlanma mevcut';
            advice = 'Dikkat! Elma tipi vücut şekli, sağlık riskleri açısından önemli. Mutlaka bir diyetisyene danışın ve düzenli kardiyovasküler egzersiz yapın.';
            bgColor = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
        }
    }
    
    // Sonucu göster
    document.getElementById('bkValue').textContent = bko;
    document.getElementById('bkCategory').innerHTML = `<strong>${category}</strong><br>${risk}`;
    document.getElementById('bkAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('bkResult');
    resultDiv.style.display = 'block';
    resultDiv.style.background = bgColor;
    
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

