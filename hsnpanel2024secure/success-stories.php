<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Başarı Hikayeleri';
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
        $stmt = $pdo->prepare("SELECT before_image, after_image FROM success_stories WHERE id = ?");
        $stmt->execute([$id]);
        $story = $stmt->fetch();
        
        if ($story) {
            if ($story['before_image']) deleteFile($story['before_image']);
            if ($story['after_image']) deleteFile($story['after_image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM success_stories WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['flash_message'] = 'success|Başarı hikayesi başarıyla silindi.';
            header('Location: success-stories.php');
            exit;
        }
    }
}

// Ekleme/Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $order_position = intval($_POST['order_position'] ?? 0);
        
        // Eğer sıra girilmemişse, otomatik olarak en son sıra + 1 yap
        if ($order_position === 0 && $id === 0) {
            $stmt = $pdo->query("SELECT MAX(order_position) as max_order FROM success_stories");
            $max = $stmt->fetch();
            $order_position = ($max['max_order'] ?? 0) + 1;
        }
        
        $active = isset($_POST['active']) ? 1 : 0;
        
        $cover_image = '';
        $before_image = '';
        $after_image = '';
        
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image = uploadFile($_FILES['cover_image'], 'img');
            if (!$cover_image) {
                $message = 'error|Kapak fotoğrafı yüklenirken hata oluştu. Lütfen JPG, PNG veya WebP formatında 2MB altında bir dosya seçin.';
            }
        }
        
        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === UPLOAD_ERR_OK) {
            $before_image = uploadFile($_FILES['before_image'], 'img');
            if (!$before_image) {
                $message = 'error|Önce fotoğrafı yüklenirken hata oluştu. Lütfen JPG, PNG veya WebP formatında 2MB altında bir dosya seçin.';
            }
        }
        
        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === UPLOAD_ERR_OK) {
            $after_image = uploadFile($_FILES['after_image'], 'img');
            if (!$after_image) {
                $message = 'error|Sonra fotoğrafı yüklenirken hata oluştu. Lütfen JPG, PNG veya WebP formatında 2MB altında bir dosya seçin.';
            }
        }
        
        if ($title && $content && !$message) {
            if ($id > 0) {
                // Güncelleme
                $updates = [];
                $params = [$title, $content, $order_position, $active];
                
                if ($cover_image) {
                    $stmt = $pdo->prepare("SELECT cover_image FROM success_stories WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['cover_image']) deleteFile($old['cover_image']);
                    $updates[] = "cover_image = ?";
                    $params[] = $cover_image;
                }
                
                if ($before_image) {
                    $stmt = $pdo->prepare("SELECT before_image FROM success_stories WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['before_image']) deleteFile($old['before_image']);
                    $updates[] = "before_image = ?";
                    $params[] = $before_image;
                }
                
                if ($after_image) {
                    $stmt = $pdo->prepare("SELECT after_image FROM success_stories WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['after_image']) deleteFile($old['after_image']);
                    $updates[] = "after_image = ?";
                    $params[] = $after_image;
                }
                
                $params[] = $id;
                $update_sql = "UPDATE success_stories SET title = ?, content = ?, order_position = ?, active = ?";
                if (count($updates) > 0) {
                    $update_sql .= ", " . implode(", ", $updates);
                }
                $update_sql .= " WHERE id = ?";
                
                $stmt = $pdo->prepare($update_sql);
                $stmt->execute($params);
                $_SESSION['flash_message'] = 'success|Başarı hikayesi başarıyla güncellendi.';
                header('Location: success-stories.php');
                exit;
            } else {
                // Ekleme - Double submit önleme
                $stmt = $pdo->prepare("INSERT INTO success_stories (title, content, cover_image, before_image, after_image, order_position, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$title, $content, $cover_image, $before_image, $after_image, $order_position, $active])) {
                    $_SESSION['flash_message'] = 'success|Başarı hikayesi başarıyla eklendi.';
                    
                    // ÖNEMLI: Hemen redirect et ve çık
                    header('Location: success-stories.php');
                    exit();
                } else {
                    $message = 'error|Başarı hikayesi eklenirken bir hata oluştu.';
                }
            }
        } else if (!$title || !$content) {
            $message = 'error|Başlık ve içerik alanları zorunludur!';
        }
    }
}

// Düzenleme için veri çek
$edit_story = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM success_stories WHERE id = ?");
    $stmt->execute([$id]);
    $edit_story = $stmt->fetch();
}

// Tüm hikayeleri listele
$stmt = $pdo->query("SELECT * FROM success_stories ORDER BY order_position ASC, id DESC");
$stories = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $edit_story ? 'Başarı Hikayesi Düzenle' : 'Yeni Başarı Hikayesi Ekle'; ?></h2>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($edit_story): ?>
            <input type="hidden" name="id" value="<?php echo $edit_story['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label class="form-label">Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_story ? clean($edit_story['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Hikaye *</label>
            <textarea name="content" class="form-control" rows="8" required><?php echo $edit_story ? clean($edit_story['content']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Kapak Fotoğrafı * <small style="color: #6c757d;">(Liste görünümünde gösterilir)</small></label>
            <input type="file" name="cover_image" class="form-control" accept="image/*" <?php echo $edit_story ? '' : 'required'; ?>>
            <?php if ($edit_story && $edit_story['cover_image']): ?>
                <div class="image-preview" style="margin-top: 15px;">
                    <img src="<?php echo imageUrl('assets/' . $edit_story['cover_image']); ?>" 
                         alt="Kapak" 
                         style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
            <?php endif; ?>
            <small class="form-text">Önerilen boyut: 800x600px | Max 2MB</small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label">Önce Fotoğrafı <small style="color: #6c757d;">(Tıklanınca gösterilir)</small></label>
                <input type="file" name="before_image" class="form-control" accept="image/*">
                <?php if ($edit_story && $edit_story['before_image']): ?>
                    <img src="/assets/<?php echo clean($edit_story['before_image']); ?>?v=<?php echo time(); ?>" style="max-width: 100%; margin-top: 10px; border-radius: 5px;">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Sonra Fotoğrafı <small style="color: #6c757d;">(Tıklanınca gösterilir)</small></label>
                <input type="file" name="after_image" class="form-control" accept="image/*">
                <?php if ($edit_story && $edit_story['after_image']): ?>
                    <img src="/assets/<?php echo clean($edit_story['after_image']); ?>?v=<?php echo time(); ?>" style="max-width: 100%; margin-top: 10px; border-radius: 5px;">
                <?php endif; ?>
            </div>
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
                   value="<?php echo $edit_story ? $edit_story['order_position'] : ''; ?>"
                   min="1"
                   placeholder="Örn: 1, 2, 3...">
            <small style="color: #6c757d; font-size: 12px; margin-top: 5px; display: block;">
                <i class="fas fa-info-circle"></i> 
                Küçük numara önce görünür (1, 2, 3...)
            </small>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_story || $edit_story['active']) ? 'checked' : ''; ?>>
                <span>Aktif</span>
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php echo $edit_story ? 'Güncelle' : 'Ekle'; ?>
        </button>
        <?php if ($edit_story): ?>
            <a href="success-stories.php" class="btn btn-sm">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Başarı Hikayeleri Listesi</h2>
    </div>
    
    <?php if (count($stories) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Önce</th>
                    <th>Sonra</th>
                    <th>Başlık</th>
                    <th>Sıra</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stories as $story): ?>
                <tr>
                    <td>
                        <?php if ($story['before_image']): ?>
                            <img src="/assets/<?php echo clean($story['before_image']); ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($story['after_image']): ?>
                            <img src="/assets/<?php echo clean($story['after_image']); ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo clean($story['title']); ?></td>
                    <td><?php echo $story['order_position']; ?></td>
                    <td>
                        <?php if ($story['active']): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Pasif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?php echo $story['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                        <a href="?delete=<?php echo $story['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                           class="btn btn-sm btn-danger btn-delete">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="padding: 20px; color: var(--text-muted);">Henüz başarı hikayesi eklenmemiş.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

