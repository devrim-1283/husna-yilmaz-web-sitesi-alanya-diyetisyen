<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Randevular';
$message = '';

// Silme işlemi
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['delete']);
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'success|Randevu başarıyla silindi.';
        }
    }
}

// Okundu işaretle
if (isset($_GET['mark_read']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['mark_read']);
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'success|Randevu okundu olarak işaretlendi.';
    }
}

// Tüm randevuları listele (type = 'appointment')
$stmt = $pdo->query("SELECT * FROM contact_messages WHERE type = 'appointment' ORDER BY is_read ASC, created_at DESC");
$appointments = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-calendar-check" style="margin-right: 8px; color: #17a2b8;"></i>
            Randevu Talepleri
        </h2>
        <div>
            <span class="badge badge-info" style="font-size: 14px; padding: 8px 12px;">
                Toplam: <?php echo count($appointments); ?>
            </span>
        </div>
    </div>
    
    <?php if (count($appointments) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 15%;">Tarih</th>
                    <th style="width: 15%;">Ad Soyad</th>
                    <th style="width: 12%;">Telefon</th>
                    <th style="width: 35%;">Detay</th>
                    <th style="width: 10%;">Durum</th>
                    <th style="width: 8%;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appt): ?>
                <tr style="<?php echo $appt['is_read'] ? '' : 'background: #e7f3ff;'; ?>" id="row-<?php echo $appt['id']; ?>">
                    <td><strong>#<?php echo $appt['id']; ?></strong></td>
                    <td>
                        <i class="far fa-calendar" style="color: #17a2b8; margin-right: 5px;"></i>
                        <?php echo date('d.m.Y', strtotime($appt['created_at'])); ?>
                        <br>
                        <small style="color: #6c757d;">
                            <i class="far fa-clock"></i>
                            <?php echo date('H:i', strtotime($appt['created_at'])); ?>
                        </small>
                    </td>
                    <td>
                        <strong><?php echo clean($appt['name'] ?? ''); ?></strong>
                        <?php if (!empty($appt['email'])): ?>
                            <br>
                            <small style="color: #6c757d;">
                                <i class="far fa-envelope"></i>
                                <?php echo clean($appt['email'] ?? ''); ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($appt['phone'])): ?>
                            <a href="tel:<?php echo clean($appt['phone'] ?? ''); ?>" 
                               style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
                                <i class="fas fa-phone-alt" style="margin-right: 5px;"></i>
                                <?php echo clean($appt['phone'] ?? ''); ?>
                            </a>
                        <?php else: ?>
                            <span style="color: #adb5bd;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="max-height: 80px; overflow-y: auto; padding: 5px; background: #f8f9fa; border-radius: 5px; font-size: 13px;">
                            <?php echo nl2br(clean($appt['message'] ?? '')); ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $appt['is_read'] ? 'success' : 'warning'; ?>" 
                              id="status-<?php echo $appt['id']; ?>"
                              style="font-size: 12px; padding: 6px 10px;">
                            <?php if ($appt['is_read']): ?>
                                <i class="fas fa-check-circle"></i> Okundu
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle"></i> Okunmadı
                            <?php endif; ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons-group">
                            <?php if (!$appt['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $appt['id']; ?>)" 
                                        class="modern-mark-read-btn"
                                        id="btn-<?php echo $appt['id']; ?>"
                                        title="Okundu olarak işaretle">
                                    <i class="fas fa-check"></i>
                                    <span>Okundu</span>
                                </button>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $appt['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                               class="modern-delete-btn"
                               onclick="return confirm('Bu randevuyu silmek istediğinize emin misiniz?');"
                               title="Randevuyu Sil">
                                <i class="fas fa-trash-alt"></i>
                                <span>Sil</span>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-calendar-times" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
        <p style="color: var(--text-muted); font-size: 16px;">Henüz randevu talebi bulunmuyor.</p>
    </div>
    <?php endif; ?>
</div>

<script>
// Mesajı okundu olarak işaretle (AJAX)
async function markAsRead(id) {
    const btnId = `btn-${id}`;
    const statusId = `status-${id}`;
    const rowId = `row-${id}`;
    const btn = document.getElementById(btnId);
    const status = document.getElementById(statusId);
    const row = document.getElementById(rowId);
    
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
            status.innerHTML = '<i class="fas fa-check-circle"></i> Okundu';
            status.style.fontSize = '12px';
            status.style.padding = '6px 10px';
            
            // Satır arka planını kaldır
            if (row) {
                row.style.background = '';
            }
            
            // Butonu gizle
            btn.style.display = 'none';
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

// Delete confirmation
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Bu randevuyu silmek istediğinize emin misiniz?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

