<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Mesajlar';
$message = '';

// Silme işlemi
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['delete']);
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'success|Mesaj başarıyla silindi.';
        }
    }
}

// Okundu işaretle
if (isset($_GET['mark_read']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['mark_read']);
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Tüm mesajları listele (Randevu talepleri HARİÇ - type = 'message')
$stmt = $pdo->query("SELECT * FROM contact_messages WHERE type = 'message' ORDER BY is_read ASC, created_at DESC");
$messages = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-envelope" style="margin-right: 8px; color: #dc3545;"></i>
            İletişim Mesajları
        </h2>
        <div>
            <span class="badge badge-danger" style="font-size: 14px; padding: 8px 12px;">
                Toplam: <?php echo count($messages); ?>
            </span>
        </div>
    </div>
    
    <?php if (count($messages) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Mesaj</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr style="<?php echo $msg['is_read'] ? '' : 'background: #fff3cd;'; ?>">
                    <td><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></td>
                    <td><?php echo clean($msg['name'] ?? ''); ?></td>
                    <td><?php echo clean($msg['email'] ?? ''); ?></td>
                    <td>
                        <?php if (!empty($msg['phone'])): ?>
                            <a href="tel:<?php echo clean($msg['phone'] ?? ''); ?>" 
                               style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                <i class="fas fa-phone-alt" style="margin-right: 5px;"></i>
                                <?php echo clean($msg['phone'] ?? ''); ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo nl2br(clean($msg['message'] ?? '')); ?></td>
                    <td>
                        <?php if ($msg['is_read']): ?>
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Okundu
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> Okunmadı
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons-group">
                            <?php if (!$msg['is_read']): ?>
                                <a href="?mark_read=<?php echo $msg['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                                   class="modern-mark-read-btn"
                                   title="Okundu olarak işaretle">
                                    <i class="fas fa-check"></i>
                                    <span>Okundu</span>
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $msg['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                               class="modern-delete-btn"
                               onclick="return confirm('Bu mesajı silmek istediğinize emin misiniz?');"
                               title="Mesajı Sil">
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
    <p class="text-center" style="padding: 20px; color: var(--text-muted);">Henüz mesaj bulunmuyor.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

