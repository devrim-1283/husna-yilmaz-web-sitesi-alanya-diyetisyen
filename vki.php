<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

$page_title = 'Vücut Kitle İndeksi (VKİ) Hesaplama';
$page_description = 'Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu öğrenin. Profesyonel diyet danışmanlığı için hemen randevu alın.';
$page_keywords = 'VKİ hesaplama, vücut kitle indeksi, BMI calculator, kilo hesaplama, ideal kilo';

// Analytics: VKİ aracı ziyareti kaydet
trackPageView('tool_vki', $_SERVER['REQUEST_URI']);

require_once __DIR__ . '/includes/header.php';
?>

<!-- VKI Hero Section -->
<section class="vki-hero">
    <div class="container">
        <div class="vki-hero-content">
            <h1><i class="fas fa-calculator"></i> Vücut Kitle İndeksi Hesaplama</h1>
            <p>Sağlık durumunuzu öğrenmek için Vücut Kitle İndeksi'nizi hesaplayın</p>
        </div>
    </div>
</section>

<!-- VKI Calculator Section -->
<section class="section">
    <div class="container">
        <div class="vki-wrapper">
            <!-- Calculator Card -->
            <div class="vki-calculator-card">
                <h2>VKİ Hesaplayıcı</h2>
                <form id="vkiForm" class="vki-form">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-weight"></i> Kilonuz (kg)
                        </label>
                        <input type="number" id="weight" class="form-control" placeholder="Örn: 70" min="1" max="300" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-ruler-vertical"></i> Boyunuz (cm)
                        </label>
                        <input type="number" id="height" class="form-control" placeholder="Örn: 175" min="50" max="250" step="0.1" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary vki-calc-btn">
                        <i class="fas fa-calculator"></i>
                        Hesapla
                    </button>
                </form>
                
                <!-- Result Display -->
                <div id="vkiResult" class="vki-result" style="display: none;">
                    <div class="vki-score">
                        <div class="vki-number" id="vkiNumber">0</div>
                        <div class="vki-unit">kg/m²</div>
                    </div>
                    <div class="vki-category" id="vkiCategory">-</div>
                    <div class="vki-description" id="vkiDescription">-</div>
                    <a href="/contact#randevu" class="btn btn-primary mt-3">
                        <i class="fas fa-calendar-check"></i>
                        Diyet Programı İçin Randevu Al
                    </a>
                </div>
            </div>
            
            <!-- Info Card -->
            <div class="vki-info-card">
                <h3><i class="fas fa-info-circle"></i> VKİ Nedir?</h3>
                <p>Vücut Kitle İndeksi (VKİ), kilo ve boy oranına göre vücut ağırlığınızın sağlıklı olup olmadığını gösteren bir ölçüdür.</p>
                
                <div class="vki-formula">
                    <h4>Formül:</h4>
                    <div class="formula-box">
                        VKİ = <span>Kilo (kg)</span> / <span>(Boy (m))²</span>
                    </div>
                </div>
                
                <h4 class="mt-4"><i class="fas fa-chart-bar"></i> VKİ Sınıflandırması</h4>
                <div class="vki-table">
                    <div class="vki-table-row vki-underweight">
                        <span class="range">< 18.5</span>
                        <span class="label">Zayıf</span>
                    </div>
                    <div class="vki-table-row vki-normal">
                        <span class="range">18.5 – 24.9</span>
                        <span class="label">Normal Kilolu</span>
                    </div>
                    <div class="vki-table-row vki-overweight">
                        <span class="range">25 – 29.9</span>
                        <span class="label">Fazla Kilolu</span>
                    </div>
                    <div class="vki-table-row vki-obese1">
                        <span class="range">30 – 34.9</span>
                        <span class="label">Obez (Tip 1)</span>
                    </div>
                    <div class="vki-table-row vki-obese2">
                        <span class="range">35 – 39.9</span>
                        <span class="label">Obez (Tip 2)</span>
                    </div>
                    <div class="vki-table-row vki-obese3">
                        <span class="range">≥ 40</span>
                        <span class="label">Morbid Obez (Tip 3)</span>
                    </div>
                </div>
                
                <div class="vki-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><strong>Önemli:</strong> VKİ genel bir göstergedir. Profesyonel bir değerlendirme için diyetisyeninize danışın.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('vkiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const weight = parseFloat(document.getElementById('weight').value);
    const heightCm = parseFloat(document.getElementById('height').value);
    const heightM = heightCm / 100;
    
    // VKİ Hesaplama
    const vki = weight / (heightM * heightM);
    const vkiRounded = vki.toFixed(2);
    
    // Kategori belirleme
    let category = '';
    let description = '';
    let categoryClass = '';
    
    if (vki < 18.5) {
        category = 'Zayıf';
        description = 'VKİ değeriniz normalin altında. Sağlıklı kilo almak için beslenme programı oluşturalım.';
        categoryClass = 'vki-underweight';
    } else if (vki >= 18.5 && vki < 25) {
        category = 'Normal Kilolu';
        description = 'Tebrikler! VKİ değeriniz ideal aralıkta. Sağlıklı beslenme ile bu kilonuzu koruyabilirsiniz.';
        categoryClass = 'vki-normal';
    } else if (vki >= 25 && vki < 30) {
        category = 'Fazla Kilolu';
        description = 'VKİ değeriniz normalin üzerinde. Kişiye özel diyet programı ile ideal kilonuza ulaşabilirsiniz.';
        categoryClass = 'vki-overweight';
    } else if (vki >= 30 && vki < 35) {
        category = 'Obez (Tip 1)';
        description = 'Sağlığınız için kilo vermeniz önerilir. Profesyonel destek ile sağlıklı kilo verebilirsiniz.';
        categoryClass = 'vki-obese1';
    } else if (vki >= 35 && vki < 40) {
        category = 'Obez (Tip 2)';
        description = 'Sağlığınız için mutlaka uzman desteği almalısınız. Birlikte sağlıklı bir program oluşturalım.';
        categoryClass = 'vki-obese2';
    } else {
        category = 'Morbid Obez (Tip 3)';
        description = 'Acil olarak uzman desteği almanız önerilir. Sağlığınız için hemen randevu alın.';
        categoryClass = 'vki-obese3';
    }
    
    // Sonuçları göster
    document.getElementById('vkiNumber').textContent = vkiRounded;
    document.getElementById('vkiCategory').textContent = category;
    document.getElementById('vkiDescription').textContent = description;
    
    const resultDiv = document.getElementById('vkiResult');
    resultDiv.className = 'vki-result ' + categoryClass;
    resultDiv.style.display = 'block';
    
    // Sonuca smooth scroll
    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>

