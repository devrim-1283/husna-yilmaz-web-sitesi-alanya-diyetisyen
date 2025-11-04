<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Sertifikalar';
$message = '';

// Flash message kontrolü (session'dan mesajı al ve temizle)
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Silme işlemi
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['delete']);
        $stmt = $pdo->prepare("SELECT image FROM certificates WHERE id = ?");
        $stmt->execute([$id]);
        $cert = $stmt->fetch();
        
        if ($cert && $cert['image']) {
            deleteFile($cert['image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['flash_message'] = 'success|Sertifika başarıyla silindi.';
            header('Location: certificates.php');
            exit;
        }
    }
}

// Ekleme/Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $issue_date = trim($_POST['issue_date'] ?? '');
        $order_position = intval($_POST['order_position'] ?? 0);
        
        // Eğer sıra girilmemişse, otomatik olarak en son sıra + 1 yap
        if ($order_position === 0 && $id === 0) {
            $stmt = $pdo->query("SELECT MAX(order_position) as max_order FROM certificates");
            $max = $stmt->fetch();
            $order_position = ($max['max_order'] ?? 0) + 1;
        }
        
        $active = isset($_POST['active']) ? 1 : 0;
        
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadFile($_FILES['image'], 'img');
            if (!$image) {
                $message = 'error|Resim yüklenirken hata oluştu. Lütfen JPG, PNG veya WebP formatında 2MB altında bir dosya seçin.';
            }
        }
        
        if ($title && !$message) {
            if ($id > 0) {
                // Güncelleme
                if ($image) {
                    // Eski resmi sil
                    $stmt = $pdo->prepare("SELECT image FROM certificates WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['image']) {
                        deleteFile($old['image']);
                    }
                    
                    $stmt = $pdo->prepare("UPDATE certificates SET title = ?, description = ?, image = ?, issue_date = ?, order_position = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $image, $issue_date, $order_position, $active, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE certificates SET title = ?, description = ?, issue_date = ?, order_position = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $issue_date, $order_position, $active, $id]);
                }
                $_SESSION['flash_message'] = 'success|Sertifika başarıyla güncellendi.';
                header('Location: certificates.php');
                exit;
            } else {
                // Ekleme - Double submit önleme
                $stmt = $pdo->prepare("INSERT INTO certificates (title, description, image, issue_date, order_position, active) VALUES (?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$title, $description, $image, $issue_date, $order_position, $active])) {
                    $_SESSION['flash_message'] = 'success|Sertifika başarıyla eklendi.';
                    
                    // ÖNEMLI: Hemen redirect et ve çık
                    header('Location: certificates.php');
                    exit();
                } else {
                    $message = 'error|Sertifika eklenirken bir hata oluştu.';
                }
            }
        } else if (!$title) {
            $message = 'error|Sertifika adı zorunludur!';
        }
    }
}

// Düzenleme için veri çek
$edit_cert = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = ?");
    $stmt->execute([$id]);
    $edit_cert = $stmt->fetch();
}

// Tüm sertifikaları listele
$stmt = $pdo->query("SELECT * FROM certificates ORDER BY order_position ASC, id DESC");
$certificates = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $edit_cert ? 'Sertifika Düzenle' : 'Yeni Sertifika Ekle'; ?></h2>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($edit_cert): ?>
            <input type="hidden" name="id" value="<?php echo $edit_cert['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label class="form-label">Sertifika Adı *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_cert ? clean($edit_cert['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Açıklama</label>
            <textarea name="description" class="form-control" rows="3"><?php echo $edit_cert ? clean($edit_cert['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Sertifika Görseli <?php echo $edit_cert ? '' : '*'; ?></label>
            <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_cert ? '' : 'required'; ?>>
            <?php if ($edit_cert && $edit_cert['image']): ?>
                <img src="/assets/<?php echo clean($edit_cert['image']); ?>?v=<?php echo time(); ?>" style="max-width: 200px; margin-top: 10px; border-radius: 5px;">
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label class="form-label">Alınma Tarihi</label>
            <input type="date" name="issue_date" class="form-control" value="<?php echo $edit_cert ? $edit_cert['issue_date'] : ''; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">
                Görüntülenme Sırası
                <small style="color: #6c757d; font-weight: normal; margin-left: 8px;">
                    (Boş bırakırsanız otomatik sıra verilir)
                </small>
            </label>
            <input type="number" 
                   name="order_position" 
                   class="form-control" 
                   value="<?php echo $edit_cert ? $edit_cert['order_position'] : ''; ?>"
                   min="1"
                   placeholder="Örn: 1, 2, 3...">
            <small style="color: #6c757d; font-size: 12px; margin-top: 5px; display: block;">
                <i class="fas fa-info-circle"></i> 
                Küçük numara önce görünür (1, 2, 3...)
            </small>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_cert || $edit_cert['active']) ? 'checked' : ''; ?>>
                <span>Aktif</span>
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php echo $edit_cert ? 'Güncelle' : 'Ekle'; ?>
        </button>
        <?php if ($edit_cert): ?>
            <a href="certificates.php" class="btn btn-sm">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Sertifikalar Listesi</h2>
    </div>
    
    <?php if (count($certificates) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Sertifika Adı</th>
                    <th>Açıklama</th>
                    <th>Alınma Tarihi</th>
                    <th>Sıra</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificates as $cert): ?>
                <tr>
                    <td>
                        <?php if ($cert['image']): ?>
                            <img src="/assets/<?php echo clean($cert['image']); ?>?v=<?php echo time(); ?>" 
                                 alt="<?php echo clean($cert['title']); ?>" 
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-certificate" style="color: #ccc; font-size: 24px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo clean($cert['title']); ?></td>
                    <td><?php echo truncate($cert['description'] ?? '', 50); ?></td>
                    <td><?php echo $cert['issue_date'] ? date('d.m.Y', strtotime($cert['issue_date'])) : '-'; ?></td>
                    <td>
                        <span class="badge badge-info" style="font-size: 14px; padding: 6px 12px;">
                            <?php echo $cert['order_position']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($cert['active']): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Pasif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?php echo $cert['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                        <a href="?delete=<?php echo $cert['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                           class="btn btn-sm btn-danger btn-delete">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="padding: 20px; color: var(--text-muted);">Henüz sertifika eklenmemiş.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

