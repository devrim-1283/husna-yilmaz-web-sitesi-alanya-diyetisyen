<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Vücut Yağ Oranı aracı ziyareti kaydet
trackPageView('tool_vucut_yag', $_SERVER['REQUEST_URI']);

$page_title = 'Vücut Yağ Oranı Hesaplama';
$page_description = 'U.S. Navy formülüyle vücut yağ oranınızı boyun, bel ve kalça ölçüleriyle hesaplayın';
$current_page = 'tools';

require_once 'config/config.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="vki-hero">
    <div class="container">
        <h1><i class="fas fa-percentage"></i> Vücut Yağ Oranı Hesaplama</h1>
        <p>Mezura ile yağ oranınızı hassas şekilde ölçün</p>
    </div>
</section>

<!-- Main Content -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2><i class="fas fa-calculator"></i> Vücut Yağ Oranı Hesaplayıcı</h2>
                
                <form id="yagOraniForm">
                    <div class="form-group">
                        <label class="form-label" for="cinsiyet">Cinsiyet *</label>
                        <select id="cinsiyet" name="cinsiyet" class="form-control" required onchange="toggleKalcaField()">
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
                        <label class="form-label" for="boyun">Boyun Çevresi (cm) *</label>
                        <input type="number" id="boyun" name="boyun" class="form-control" placeholder="Boyun çevrenizi girin" min="1" step="0.1" required>
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Gırtlağın hemen altından ölçün</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="bel">Bel Çevresi (cm) *</label>
                        <input type="number" id="bel" name="bel" class="form-control" placeholder="Bel çevrenizi girin" min="1" step="0.1" required>
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Göbek deliği hizasından ölçün</small>
                    </div>
                    
                    <div class="form-group" id="kalcaGroup" style="display: none;">
                        <label class="form-label" for="kalca">Kalça Çevresi (cm) *</label>
                        <input type="number" id="kalca" name="kalca" class="form-control" placeholder="Kalça çevrenizi girin" min="1" step="0.1">
                        <small style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Kalçanın en geniş yerinden ölçün</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Yağ Oranını Hesapla
                    </button>
                </form>

                <div id="yagOraniResult" class="vki-result" style="display: none;">
                    <div class="result-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h3>Vücut Yağ Oranınız</h3>
                    <div class="result-value">
                        <span id="yagOraniValue"></span>
                    </div>
                    <p id="yagOraniCategory"></p>
                    <div class="result-advice">
                        <i class="fas fa-info-circle"></i>
                        <p id="yagOraniAdvice"></p>
                    </div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> Vücut Yağ Oranı Nedir?</h3>
                <p>Vücut yağ oranı, vücut ağırlığınızın yüzde kaçının yağ dokusu olduğunu gösterir. U.S. Navy formülü, mezura ile doğru sonuç veren güvenilir bir yöntemdir.</p>
                
                <div class="vki-formula">
                    <h4><i class="fas fa-flask"></i> U.S. Navy Body Fat Formülü</h4>
                    <div class="formula-box">
                        <strong>Kadınlar:</strong><br>
                        Yağ % = 163.205 × log₁₀(<span>Bel + Kalça - Boy</span>) - 97.684 × log₁₀(<span>Boyun</span>) - 78.387
                    </div>
                    <div class="formula-box" style="margin-top: 15px;">
                        <strong>Erkekler:</strong><br>
                        Yağ % = 86.010 × log₁₀(<span>Bel - Boyun</span>) - 70.041 × log₁₀(<span>Boy</span>) + 36.76
                    </div>
                </div>

                <h4><i class="fas fa-ruler"></i> Ölçüm Kuralları</h4>
                <ul>
                    <li><strong>Boyun:</strong> Gırtlağın hemen altından, mezura sıkmadan</li>
                    <li><strong>Bel (Erkek):</strong> Göbek deliği hizası</li>
                    <li><strong>Bel (Kadın):</strong> Belin en ince noktası</li>
                    <li><strong>Kalça:</strong> Kalçanın en geniş kısmı (sadece kadınlar)</li>
                    <li><strong>Zaman:</strong> Sabah aç karnına ölçüm yapın</li>
                </ul>

                <h4><i class="fas fa-chart-line"></i> Yağ Oranı Değerlendirme</h4>
                
                <div class="vki-table">
                    <h5 style="color: var(--primary-green); margin-bottom: 10px;">👨 Erkekler İçin</h5>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Atletik</span>
                        <span class="range">6-13%</span>
                    </div>
                    <div class="vki-table-row" style="background: #c8e6c9;">
                        <span class="category">Fit</span>
                        <span class="range">14-17%</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff9c4;">
                        <span class="category">Ortalama</span>
                        <span class="range">18-24%</span>
                    </div>
                    <div class="vki-table-row" style="background: #ffccbc;">
                        <span class="category">Fazla Yağlı / Obez</span>
                        <span class="range">≥ 25%</span>
                    </div>
                </div>

                <div class="vki-table" style="margin-top: 20px;">
                    <h5 style="color: var(--primary-green); margin-bottom: 10px;">👩 Kadınlar İçin</h5>
                    <div class="vki-table-row" style="background: #e8f5e9;">
                        <span class="category">Atletik</span>
                        <span class="range">14-20%</span>
                    </div>
                    <div class="vki-table-row" style="background: #c8e6c9;">
                        <span class="category">Fit</span>
                        <span class="range">21-24%</span>
                    </div>
                    <div class="vki-table-row" style="background: #fff9c4;">
                        <span class="category">Ortalama</span>
                        <span class="range">25-31%</span>
                    </div>
                    <div class="vki-table-row" style="background: #ffccbc;">
                        <span class="category">Fazla Yağlı / Obez</span>
                        <span class="range">≥ 32%</span>
                    </div>
                </div>

                <div class="vki-info-card" style="background: rgba(255, 243, 205, 0.3); border-left: 4px solid #ff9800; padding: 15px; margin-top: 20px;">
                    <p><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <strong>Uyarı:</strong> Çok düşük yağ oranı (%10 altı erkek, %14 altı kadın) hormon dengesizliği ve sağlık sorunlarına yol açabilir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleKalcaField() {
    const cinsiyet = document.getElementById('cinsiyet').value;
    const kalcaGroup = document.getElementById('kalcaGroup');
    const kalcaInput = document.getElementById('kalca');
    
    if (cinsiyet === 'kadin') {
        kalcaGroup.style.display = 'block';
        kalcaInput.required = true;
    } else {
        kalcaGroup.style.display = 'none';
        kalcaInput.required = false;
        kalcaInput.value = '';
    }
}

document.getElementById('yagOraniForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cinsiyet = document.getElementById('cinsiyet').value;
    const boy = parseFloat(document.getElementById('boy').value);
    const boyun = parseFloat(document.getElementById('boyun').value);
    const bel = parseFloat(document.getElementById('bel').value);
    const kalca = parseFloat(document.getElementById('kalca').value) || 0;
    
    if (!cinsiyet || !boy || !boyun || !bel) {
        alert('Lütfen tüm alanları doldurun!');
        return;
    }
    
    if (cinsiyet === 'kadin' && !kalca) {
        alert('Kadınlar için kalça ölçüsü gereklidir!');
        return;
    }
    
    // U.S. Navy Body Fat Formula
    let yagOrani;
    
    if (cinsiyet === 'erkek') {
        // Erkek formülü
        const param1 = bel - boyun;
        yagOrani = (86.010 * Math.log10(param1)) - (70.041 * Math.log10(boy)) + 36.76;
    } else {
        // Kadın formülü
        const param1 = bel + kalca - boy;
        yagOrani = (163.205 * Math.log10(param1)) - (97.684 * Math.log10(boyun)) - 78.387;
    }
    
    yagOrani = Math.max(0, yagOrani); // Negatif değerleri önle
    
    // Değerlendirme
    let category, risk, advice, bgColor;
    
    if (cinsiyet === 'erkek') {
        if (yagOrani < 6) {
            category = 'Çok Düşük ⚠️';
            risk = 'Sağlık riski var';
            advice = 'Dikkat! Vücut yağ oranınız çok düşük. Bu hormon dengesizliği ve sağlık sorunlarına yol açabilir. Mutlaka bir doktora danışın.';
            bgColor = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        } else if (yagOrani <= 13) {
            category = 'Atletik';
            risk = 'Mükemmel yağ oranı';
            advice = 'Harika! Vücut yağ oranınız atletik düzeyde. Bu oranı korumak için düzenli egzersiz ve dengeli beslenmeye devam edin.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (yagOrani <= 17) {
            category = 'Fit';
            risk = 'Çok iyi';
            advice = 'Tebrikler! Vücut yağ oranınız fit ve sağlıklı aralıkta. Mevcut yaşam tarzınızı sürdürün.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (yagOrani <= 24) {
            category = 'Ortalama';
            risk = 'Kabul edilebilir';
            advice = 'Vücut yağ oranınız ortalama aralıkta. Daha fit olmak istiyorsanız düzenli egzersiz ve kalori kontrolü yapın.';
            bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
        } else {
            category = 'Fazla Yağlı';
            risk = 'Sağlık riski artıyor';
            advice = 'Dikkat! Vücut yağ oranınız yüksek. Kilo vermek ve yağ yakmak için mutlaka bir diyetisyene danışın ve düzenli egzersiz programı başlatın.';
            bgColor = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
        }
    } else { // kadın
        if (yagOrani < 14) {
            category = 'Çok Düşük ⚠️';
            risk = 'Sağlık riski var';
            advice = 'Dikkat! Vücut yağ oranınız çok düşük. Bu hormon dengesizliği, adet düzensizliği ve kemik erimesine yol açabilir. Mutlaka bir doktora danışın.';
            bgColor = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        } else if (yagOrani <= 20) {
            category = 'Atletik';
            risk = 'Mükemmel yağ oranı';
            advice = 'Harika! Vücut yağ oranınız atletik düzeyde. Bu oranı korumak için düzenli egzersiz ve dengeli beslenmeye devam edin.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (yagOrani <= 24) {
            category = 'Fit';
            risk = 'Çok iyi';
            advice = 'Tebrikler! Vücut yağ oranınız fit ve sağlıklı aralıkta. Mevcut yaşam tarzınızı sürdürün.';
            bgColor = 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';
        } else if (yagOrani <= 31) {
            category = 'Ortalama';
            risk = 'Kabul edilebilir';
            advice = 'Vücut yağ oranınız ortalama aralıkta. Daha fit olmak istiyorsanız düzenli egzersiz ve dengeli beslenme yapın.';
            bgColor = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
        } else {
            category = 'Fazla Yağlı';
            risk = 'Sağlık riski artıyor';
            advice = 'Dikkat! Vücut yağ oranınız yüksek. Sağlıklı kilo vermek ve yağ oranını düşürmek için mutlaka bir diyetisyene danışın.';
            bgColor = 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)';
        }
    }
    
    // Sonucu göster
    document.getElementById('yagOraniValue').textContent = '%' + yagOrani.toFixed(1);
    document.getElementById('yagOraniCategory').innerHTML = `<strong>${category}</strong><br>${risk}`;
    document.getElementById('yagOraniAdvice').textContent = advice;
    
    // Sonuç kartını göster ve scroll
    const resultDiv = document.getElementById('yagOraniResult');
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

