<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/analytics.php';
requireAdmin();

$page_title = 'Araç İstatistikleri';

// Tarih aralığı seç (varsayılan 30 gün)
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;
if ($days < 1) $days = 30;
if ($days > 365) $days = 365;

// Pagination parametreleri
$tool_page = isset($_GET['tool_page']) ? intval($_GET['tool_page']) : 1;
if ($tool_page < 1) $tool_page = 1;
$per_page = 10; // Sayfa başına 10 araç

// Araç isimlerini düzenle
$toolNames = [
    'tool_vki' => 'VKİ Hesaplama',
    'tool_bmh' => 'BMH Hesaplama',
    'tool_bel_kalca' => 'Bel/Kalça Oranı',
    'tool_kalori' => 'Günlük Kalori',
    'tool_karbonhidrat' => 'Günlük Karbonhidrat',
    'tool_makro' => 'Günlük Makro Besin',
    'tool_protein' => 'Günlük Protein',
    'tool_su' => 'Günlük Su',
    'tool_yag' => 'Günlük Yağ',
    'tool_ideal_kilo' => 'İdeal Kilo',
    'tool_vucut_yag' => 'Vücut Yağ Oranı'
];

// Araç kullanım istatistikleri
try {
    // Toplam araç kullanımı
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_tool_usage
        FROM page_views 
        WHERE page_type LIKE 'tool_%'
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->execute([$days]);
    $totalToolUsage = $stmt->fetchColumn();
    
    // Toplam araç sayısı (pagination için)
    $count_stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT page_type) as total
        FROM page_views 
        WHERE page_type LIKE 'tool_%'
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $count_stmt->execute([$days]);
    $total_tools = $count_stmt->fetchColumn();
    
    // Pagination hesapla
    $total_tool_pages = ceil($total_tools / $per_page);
    $offset = ($tool_page - 1) * $per_page;
    
    // Araç bazında kullanım (PAGINATION ile)
    $stmt = $pdo->prepare("
        SELECT 
            page_type,
            COUNT(*) as usage_count,
            COUNT(DISTINCT session_id) as unique_users
        FROM page_views 
        WHERE page_type LIKE 'tool_%'
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY page_type
        ORDER BY usage_count DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$days, $per_page, $offset]);
    $toolStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Günlük kullanım trendi
    $stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as usage_count
        FROM page_views 
        WHERE page_type LIKE 'tool_%'
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$days]);
    $dailyToolTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Saatlik dağılım (En popüler saatler)
    $stmt = $pdo->prepare("
        SELECT 
            HOUR(created_at) as hour,
            COUNT(*) as usage_count
        FROM page_views 
        WHERE page_type LIKE 'tool_%'
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY HOUR(created_at)
        ORDER BY hour ASC
    ");
    $stmt->execute([$days]);
    $hourlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Tool Stats Error: " . $e->getMessage());
    $toolStats = [];
    $dailyToolTrend = [];
    $hourlyStats = [];
    $totalToolUsage = 0;
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
.tool-stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.tool-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.tool-icon {
    width: 60px;
    height: 60px;
    min-width: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
}

.tool-info {
    flex: 1;
    min-width: 0;
}

.tool-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.tool-usage {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 5px;
}

.tool-unique {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.progress-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
    transition: width 0.3s ease;
}
</style>

<div class="tool-stat-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0;">
            <i class="fas fa-calculator" style="color: var(--primary); margin-right: 10px;"></i>
            Araç Kullanım İstatistikleri
        </h2>
        
        <div class="filter-buttons">
            <a href="?days=7" class="btn btn-sm <?php echo $days == 7 ? 'btn-primary' : ''; ?>">7 Gün</a>
            <a href="?days=30" class="btn btn-sm <?php echo $days == 30 ? 'btn-primary' : ''; ?>">30 Gün</a>
            <a href="?days=90" class="btn btn-sm <?php echo $days == 90 ? 'btn-primary' : ''; ?>">90 Gün</a>
            <a href="?days=365" class="btn btn-sm <?php echo $days == 365 ? 'btn-primary' : ''; ?>">1 Yıl</a>
        </div>
    </div>
    
    <!-- Toplam Özet -->
    <div style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 30px; border-radius: 12px; text-align: center; margin-bottom: 30px;">
        <h3 style="margin: 0; font-size: 3rem; font-weight: 700;"><?php echo number_format($totalToolUsage); ?></h3>
        <p style="margin: 10px 0 0 0; font-size: 1.1rem; opacity: 0.9;">
            <i class="fas fa-chart-bar"></i> Toplam Araç Kullanımı (Son <?php echo $days; ?> Gün)
        </p>
    </div>
</div>

<!-- Araç Bazında İstatistikler -->
<div class="tool-stat-card">
    <h3 style="margin-bottom: 25px;">
        <i class="fas fa-tools" style="color: var(--primary); margin-right: 8px;"></i>
        Araç Bazında Kullanım
    </h3>
    
    <?php if (count($toolStats) > 0): ?>
        <?php 
        $maxUsage = max(array_column($toolStats, 'usage_count'));
        foreach ($toolStats as $tool): 
            $toolName = $toolNames[$tool['page_type']] ?? $tool['page_type'];
            $percentage = $maxUsage > 0 ? ($tool['usage_count'] / $maxUsage) * 100 : 0;
        ?>
        <div style="display: flex; gap: 20px; align-items: center; padding: 20px; background: #f8f9fa; border-radius: 10px; margin-bottom: 15px;">
            <div class="tool-icon">
                <i class="fas fa-calculator"></i>
            </div>
            
            <div class="tool-info">
                <div class="tool-name"><?php echo clean($toolName); ?></div>
                <div style="display: flex; gap: 30px; align-items: baseline;">
                    <div>
                        <span class="tool-usage"><?php echo number_format($tool['usage_count']); ?></span>
                        <span class="tool-unique">kullanım</span>
                    </div>
                    <div>
                        <span style="font-size: 1.3rem; font-weight: 600; color: #FF9800;">
                            <?php echo number_format($tool['unique_users']); ?>
                        </span>
                        <span class="tool-unique">benzersiz kullanıcı</span>
                    </div>
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Pagination -->
        <?php if ($total_tool_pages > 1): ?>
        <div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
            <?php if ($tool_page > 1): ?>
            <a href="?days=<?php echo $days; ?>&tool_page=<?php echo $tool_page - 1; ?>" class="btn btn-sm">
                <i class="fas fa-chevron-left"></i> Önceki
            </a>
            <?php endif; ?>
            
            <span style="padding: 8px 15px; background: #f0f0f0; border-radius: 8px; font-weight: 600;">
                Sayfa <?php echo $tool_page; ?> / <?php echo $total_tool_pages; ?>
            </span>
            
            <?php if ($tool_page < $total_tool_pages): ?>
            <a href="?days=<?php echo $days; ?>&tool_page=<?php echo $tool_page + 1; ?>" class="btn btn-sm">
                Sonraki <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 40px;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i><br>
            Henüz araç kullanımı verisi yok.
        </p>
    <?php endif; ?>
</div>

<!-- Günlük Trend Grafiği -->
<?php if (count($dailyToolTrend) > 0): ?>
<div class="tool-stat-card">
    <h3 style="margin-bottom: 25px;">
        <i class="fas fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i>
        Günlük Kullanım Trendi
    </h3>
    
    <div style="position: relative; height: 300px;">
        <canvas id="dailyTrendChart"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Saatlik Dağılım -->
<?php if (count($hourlyStats) > 0): ?>
<div class="tool-stat-card">
    <h3 style="margin-bottom: 25px;">
        <i class="fas fa-clock" style="color: var(--primary); margin-right: 8px;"></i>
        Saatlik Kullanım Dağılımı (En Popüler Saatler)
    </h3>
    
    <div style="position: relative; height: 300px;">
        <canvas id="hourlyChart"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Günlük Trend Grafiği
<?php if (count($dailyToolTrend) > 0): ?>
const dailyData = <?php echo json_encode($dailyToolTrend); ?>;
const dailyLabels = dailyData.map(d => d.date);
const dailyValues = dailyData.map(d => parseInt(d.usage_count));

const ctxDaily = document.getElementById('dailyTrendChart').getContext('2d');
new Chart(ctxDaily, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Araç Kullanımı',
            data: dailyValues,
            borderColor: '#FF9800',
            backgroundColor: 'rgba(255, 152, 0, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
<?php endif; ?>

// Saatlik Dağılım Grafiği
<?php if (count($hourlyStats) > 0): ?>
const hourlyData = <?php echo json_encode($hourlyStats); ?>;
const hourlyLabels = hourlyData.map(d => d.hour + ':00');
const hourlyValues = hourlyData.map(d => parseInt(d.usage_count));

const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
new Chart(ctxHourly, {
    type: 'bar',
    data: {
        labels: hourlyLabels,
        datasets: [{
            label: 'Kullanım Sayısı',
            data: hourlyValues,
            backgroundColor: 'rgba(255, 152, 0, 0.7)',
            borderColor: '#FF9800',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

