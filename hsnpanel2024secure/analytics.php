<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/analytics.php';
requireAdmin();

$page_title = 'Analitik & İstatistikler';

// Tarih aralığı seç (varsayılan 7 gün)
$days = isset($_GET['days']) ? intval($_GET['days']) : 7;
if ($days < 1) $days = 7;
if ($days > 90) $days = 90;

// Pagination parametreleri
$page_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page_page < 1) $page_page = 1;
$per_page = 10; // Sayfa başına 10 kayıt

// Analytics istatistiklerini al (pagination ile)
$stats = getAnalyticsStats($days, $page_page, $per_page);

require_once __DIR__ . '/includes/header.php';
?>

<style>
.analytics-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.stat-box {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 15px;
}

.stat-box h3 {
    margin: 0 0 10px 0;
    font-size: 2.5rem;
    font-weight: 700;
}

.stat-box p {
    margin: 0;
    font-size: 0.95rem;
    opacity: 0.9;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-top: 20px;
}

.table-responsive {
    overflow-x: auto;
}

.page-type-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-index {
    background: #e3f2fd;
    color: #1976d2;
}

.badge-blog {
    background: #f3e5f5;
    color: #7b1fa2;
}

.badge-tool {
    background: #e8f5e9;
    color: #388e3c;
}

.filter-buttons {
    margin-bottom: 20px;
}

.filter-buttons .btn {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>

<div class="analytics-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0;">
            <i class="fas fa-chart-line" style="color: var(--primary); margin-right: 10px;"></i>
            Analitik & İstatistikler
        </h2>
        
        <div class="filter-buttons">
            <a href="?days=7" class="btn btn-sm <?php echo $days == 7 ? 'btn-primary' : ''; ?>">Son 7 Gün</a>
            <a href="?days=30" class="btn btn-sm <?php echo $days == 30 ? 'btn-primary' : ''; ?>">Son 30 Gün</a>
            <a href="?days=90" class="btn btn-sm <?php echo $days == 90 ? 'btn-primary' : ''; ?>">Son 90 Gün</a>
        </div>
    </div>
    
    <!-- Genel İstatistikler -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-box" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);">
            <h3><?php echo number_format($stats['totals']['total_views']); ?></h3>
            <p><i class="fas fa-eye"></i> Toplam Görüntülenme</p>
        </div>
        
        <div class="stat-box" style="background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);">
            <h3><?php echo number_format($stats['totals']['unique_sessions']); ?></h3>
            <p><i class="fas fa-users"></i> Benzersiz Oturum</p>
        </div>
        
        <div class="stat-box" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
            <h3><?php echo number_format($stats['totals']['unique_ips']); ?></h3>
            <p><i class="fas fa-globe"></i> Benzersiz IP</p>
        </div>
        
        <div class="stat-box" style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
            <h3><?php echo $stats['totals']['total_views'] > 0 ? number_format($stats['totals']['total_views'] / max($days, 1), 1) : '0'; ?></h3>
            <p><i class="fas fa-chart-line"></i> Günlük Ortalama</p>
        </div>
    </div>
</div>

<!-- Sayfa Bazında İstatistikler -->
<div class="analytics-card">
    <h3 style="margin-bottom: 20px;">
        <i class="fas fa-file-alt" style="color: var(--primary); margin-right: 8px;"></i>
        Sayfa Bazında Görüntülenme
    </h3>
    
    <?php if (count($stats['by_page']) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Sayfa Tipi</th>
                    <th style="text-align: center;">Görüntülenme</th>
                    <th style="text-align: center;">Yüzde</th>
                    <th>Grafik</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalViews = $stats['totals']['total_views'];
                
                // Sayfa isim çevirileri
                $pageNames = [
                    'ana_sayfa' => 'Ana Sayfa',
                    'hakkimda' => 'Hakkımda',
                    'hizmetler' => 'Hizmetlerim',
                    'basari_hikayeleri' => 'Başarı Hikayeleri',
                    'blog_liste' => 'Blog Listesi',
                    'blog_detay' => 'Blog Detay',
                    'iletisim' => 'İletişim',
                    'tool_vki' => 'VKİ Hesaplama',
                    'tool_bmh' => 'BMH Hesaplama',
                    'tool_bel_kalca' => 'Bel/Kalça Oranı',
                    'tool_kalori' => 'Günlük Kalori',
                    'tool_karbonhidrat' => 'Günlük Karbonhidrat',
                    'tool_makro' => 'Günlük Makro',
                    'tool_protein' => 'Günlük Protein',
                    'tool_su' => 'Günlük Su',
                    'tool_yag' => 'Günlük Yağ',
                    'tool_ideal_kilo' => 'İdeal Kilo',
                    'tool_vucut_yag' => 'Vücut Yağ Oranı',
                    // Eski değerler (geriye uyumluluk)
                    'index' => 'Ana Sayfa',
                    'blog' => 'Blog Detay'
                ];
                
                foreach ($stats['by_page'] as $page): 
                    $percentage = $totalViews > 0 ? ($page['view_count'] / $totalViews) * 100 : 0;
                    
                    // Badge rengi belirle
                    $badgeClass = 'badge-tool';
                    if (in_array($page['page_type'], ['ana_sayfa', 'index'])) $badgeClass = 'badge-index';
                    else if (in_array($page['page_type'], ['blog_detay', 'blog', 'blog_liste'])) $badgeClass = 'badge-blog';
                    else if (strpos($page['page_type'], 'tool_') === 0) $badgeClass = 'badge-tool';
                    
                    // Sayfa adını al
                    $pageName = $pageNames[$page['page_type']] ?? ucfirst(str_replace('_', ' ', $page['page_type']));
                ?>
                <tr>
                    <td>
                        <span class="page-type-badge <?php echo $badgeClass; ?>">
                            <?php echo clean($pageName); ?>
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: 600;">
                        <?php echo number_format($page['view_count']); ?>
                    </td>
                    <td style="text-align: center;">
                        <?php echo number_format($percentage, 1); ?>%
                    </td>
                    <td>
                        <div style="background: #e0e0e0; border-radius: 10px; height: 10px; overflow: hidden;">
                            <div style="background: var(--primary); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($stats['pagination']['total_pages'] > 1): ?>
    <div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
        <?php if ($page_page > 1): ?>
        <a href="?days=<?php echo $days; ?>&page=<?php echo $page_page - 1; ?>" class="btn btn-sm">
            <i class="fas fa-chevron-left"></i> Önceki
        </a>
        <?php endif; ?>
        
        <span style="padding: 8px 15px; background: #f0f0f0; border-radius: 8px; font-weight: 600;">
            Sayfa <?php echo $page_page; ?> / <?php echo $stats['pagination']['total_pages']; ?>
        </span>
        
        <?php if ($page_page < $stats['pagination']['total_pages']): ?>
        <a href="?days=<?php echo $days; ?>&page=<?php echo $page_page + 1; ?>" class="btn btn-sm">
            Sonraki <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <p style="text-align: center; color: var(--text-muted); padding: 20px;">
        Henüz veri yok.
    </p>
    <?php endif; ?>
</div>

<!-- Günlük Trend -->
<div class="analytics-card">
    <h3 style="margin-bottom: 20px;">
        <i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 8px;"></i>
        Günlük Görüntülenme Trendi
    </h3>
    
    <?php if (count($stats['daily_trend']) > 0): ?>
    <div class="chart-container">
        <canvas id="dailyChart"></canvas>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: var(--text-muted); padding: 20px;">
        Henüz veri yok.
    </p>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
<?php if (count($stats['daily_trend']) > 0): ?>
const dailyData = <?php echo json_encode($stats['daily_trend']); ?>;
const labels = dailyData.map(d => d.date);
const values = dailyData.map(d => parseInt(d.view_count));

const ctx = document.getElementById('dailyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Görüntülenme',
            data: values,
            borderColor: 'rgb(46, 125, 50)',
            backgroundColor: 'rgba(46, 125, 50, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

