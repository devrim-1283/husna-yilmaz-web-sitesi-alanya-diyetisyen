<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/analytics.php';
requireAdmin();

$page_title = 'Dashboard';

// İstatistikleri al
$stats = [];

// Toplam hizmet sayısı
$stmt = $pdo->query("SELECT COUNT(*) FROM services WHERE active = 1");
$stats['services'] = $stmt->fetchColumn();

// Toplam başarı hikayesi
$stmt = $pdo->query("SELECT COUNT(*) FROM success_stories WHERE active = 1");
$stats['stories'] = $stmt->fetchColumn();

// Toplam blog
$stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE active = 1");
$stats['blogs'] = $stmt->fetchColumn();

// Okunmamış mesajlar
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$stats['unread_messages'] = $stmt->fetchColumn();

// Toplam mesajlar
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
$stats['total_messages'] = $stmt->fetchColumn();

// Randevu Talepleri (type = 'appointment')
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE type = 'appointment'");
$stats['appointments'] = $stmt->fetchColumn();

// Diğer Mesajlar (type = 'message')
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE type = 'message'");
$stats['other_messages'] = $stmt->fetchColumn();

// Son randevu talepleri
$stmt = $pdo->query("SELECT * FROM contact_messages WHERE type = 'appointment' ORDER BY created_at DESC LIMIT 5");
$recent_appointments = $stmt->fetchAll();

// Son mesajlar (randevu hariç)
$stmt = $pdo->query("SELECT * FROM contact_messages WHERE type = 'message' ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();

// Son bloglar
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5");
$recent_blogs = $stmt->fetchAll();

// Analytics istatistikleri (Son 7 gün)
$analytics_stats = getAnalyticsStats(7);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Analytics Widget (Son 7 Gün) -->
<div class="card" style="margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h2 style="margin: 0; color: white;">
                <i class="fas fa-chart-line"></i> Son 7 Günlük Analitik
            </h2>
            <a href="analytics.php" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: none;">
                <i class="fas fa-arrow-right"></i> Detaylı Rapor
            </a>
        </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; padding: 20px;">
        <div style="text-align: center;">
            <h3 style="margin: 0; font-size: 2.5rem; font-weight: 700;"><?php echo number_format($analytics_stats['totals']['total_views']); ?></h3>
            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Toplam Görüntülenme</p>
        </div>
        <div style="text-align: center;">
            <h3 style="margin: 0; font-size: 2.5rem; font-weight: 700;"><?php echo number_format($analytics_stats['totals']['unique_sessions']); ?></h3>
            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Benzersiz Ziyaretçi</p>
        </div>
        <div style="text-align: center;">
            <h3 style="margin: 0; font-size: 2.5rem; font-weight: 700;"><?php echo $analytics_stats['totals']['total_views'] > 0 ? number_format($analytics_stats['totals']['total_views'] / 7, 1) : '0'; ?></h3>
            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Günlük Ortalama</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-concierge-bell"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['services']; ?></h3>
            <p>Aktif Hizmet</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['stories']; ?></h3>
            <p>Başarı Hikayesi</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-blog"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['blogs']; ?></h3>
            <p>Blog Yazısı</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['appointments']; ?></h3>
            <p>Randevu Talebi</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['other_messages']; ?></h3>
            <p>İletişim Mesajı</p>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-calendar-check" style="margin-right: 8px; color: #17a2b8;"></i>
            Son Randevu Talepleri
        </h2>
        <a href="appointments.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
    </div>
    
    <?php if (count($recent_appointments) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>Telefon</th>
                    <th>Detay</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_appointments as $msg): ?>
                <tr>
                    <td><?php echo clean($msg['name'] ?? ''); ?></td>
                    <td>
                        <?php if (!empty($msg['phone'])): ?>
                            <a href="tel:<?php echo clean($msg['phone'] ?? ''); ?>" 
                               style="color: var(--primary); text-decoration: none;">
                                <i class="fas fa-phone-alt" style="margin-right: 5px;"></i>
                                <?php echo clean($msg['phone'] ?? ''); ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo truncate($msg['message'] ?? '', 50); ?></td>
                    <td><?php echo formatDate($msg['created_at']); ?></td>
                    <td>
                        <div class="action-buttons-group">
                            <span class="badge badge-<?php echo $msg['is_read'] ? 'success' : 'warning'; ?>" id="status-appt-<?php echo $msg['id']; ?>">
                                <?php if ($msg['is_read']): ?>
                                    <i class="fas fa-check-circle"></i> Okundu
                                <?php else: ?>
                                    <i class="fas fa-clock"></i> Okunmadı
                                <?php endif; ?>
                            </span>
                            <?php if (!$msg['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $msg['id']; ?>, 'appt')" 
                                        class="modern-mark-read-btn"
                                        id="btn-appt-<?php echo $msg['id']; ?>"
                                        title="Okundu olarak işaretle">
                                    <i class="fas fa-check"></i>
                                    <span>Okundu</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="color: var(--text-muted);">Henüz randevu talebi bulunmuyor.</p>
    <?php endif; ?>
</div>

<!-- Recent Messages -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-envelope" style="margin-right: 8px; color: #dc3545;"></i>
            Son İletişim Mesajları
        </h2>
        <a href="messages.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
    </div>
    
    <?php if (count($recent_messages) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>Telefon</th>
                    <th>Mesaj</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_messages as $msg): ?>
                <tr>
                    <td><?php echo clean($msg['name'] ?? ''); ?></td>
                    <td>
                        <?php if (!empty($msg['phone'])): ?>
                            <a href="tel:<?php echo clean($msg['phone'] ?? ''); ?>" 
                               style="color: var(--primary); text-decoration: none;">
                                <i class="fas fa-phone-alt" style="margin-right: 5px;"></i>
                                <?php echo clean($msg['phone'] ?? ''); ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo truncate($msg['message'] ?? '', 50); ?></td>
                    <td><?php echo formatDate($msg['created_at']); ?></td>
                    <td>
                        <div class="action-buttons-group">
                            <span class="badge badge-<?php echo $msg['is_read'] ? 'success' : 'warning'; ?>" id="status-msg-<?php echo $msg['id']; ?>">
                                <?php if ($msg['is_read']): ?>
                                    <i class="fas fa-check-circle"></i> Okundu
                                <?php else: ?>
                                    <i class="fas fa-clock"></i> Okunmadı
                                <?php endif; ?>
                            </span>
                            <?php if (!$msg['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $msg['id']; ?>, 'msg')" 
                                        class="modern-mark-read-btn"
                                        id="btn-msg-<?php echo $msg['id']; ?>"
                                        title="Okundu olarak işaretle">
                                    <i class="fas fa-check"></i>
                                    <span>Okundu</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="color: var(--text-muted);">Henüz mesaj bulunmuyor.</p>
    <?php endif; ?>
</div>

<!-- Recent Blogs -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Son Blog Yazıları</h2>
        <a href="blogs.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
    </div>
    
    <?php if (count($recent_blogs) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Slug</th>
                    <th>Görüntülenme</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_blogs as $blog): ?>
                <tr>
                    <td><?php echo clean($blog['title']); ?></td>
                    <td><?php echo clean($blog['slug']); ?></td>
                    <td><?php echo $blog['views']; ?></td>
                    <td><?php echo formatDate($blog['created_at']); ?></td>
                    <td>
                        <?php if ($blog['active']): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Pasif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="color: var(--text-muted);">Henüz blog yazısı bulunmuyor.</p>
    <?php endif; ?>
</div>

<script>
// Mesajı okundu olarak işaretle (AJAX)
async function markAsRead(id, type) {
    const btnId = `btn-${type}-${id}`;
    const statusId = `status-${type}-${id}`;
    const btn = document.getElementById(btnId);
    const status = document.getElementById(statusId);
    
    if (!btn || !status) return;
    
    // Butonu devre dışı bırak
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('mark_read.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Badge'i güncelle
            status.className = 'badge badge-success';
            status.textContent = 'Okundu';
            
            // Butonu gizle
            btn.style.display = 'none';
            
            // Başarı mesajı (opsiyonel)
            // alert('✅ ' + result.message);
        } else {
            alert('❌ ' + result.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i>';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Bir hata oluştu!');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i>';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

