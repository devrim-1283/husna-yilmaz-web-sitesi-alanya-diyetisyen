<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/update_sitemap_trigger.php';
requireAdmin();

$page_title = 'Bloglar';
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
        $stmt = $pdo->prepare("SELECT image FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $blog = $stmt->fetch();
        
        if ($blog && $blog['image']) {
            deleteFile($blog['image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['flash_message'] = 'success|Blog yazısı başarıyla silindi.';
            updateSitemap(); // Sitemap'i güncelle
            header('Location: blogs.php');
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
        $meta_description = trim($_POST['meta_description'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;
        
        // Slug otomatik oluştur
        if (!$slug) {
            $slug = createSlug($title);
        } else {
            $slug = createSlug($slug);
        }
        
        // Okuma süresini hesapla
        $reading_time = calculateReadingTime($content);
        
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadFile($_FILES['image'], 'img');
            if (!$image) {
                $message = 'error|Resim yüklenirken hata oluştu. Lütfen JPG, PNG veya WebP formatında 2MB altında bir dosya seçin.';
            }
        }
        
        if ($title && $content && !$message) {
            if ($id > 0) {
                // Güncelleme
                if ($image) {
                    // Eski resmi sil
                    $stmt = $pdo->prepare("SELECT image FROM blogs WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['image']) {
                        deleteFile($old['image']);
                    }
                    
                    $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, meta_description = ?, image = ?, slug = ?, reading_time = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $content, $meta_description, $image, $slug, $reading_time, $active, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, meta_description = ?, slug = ?, reading_time = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $content, $meta_description, $slug, $reading_time, $active, $id]);
                }
                $_SESSION['flash_message'] = 'success|Blog yazısı başarıyla güncellendi.';
                      updateSitemap(); // Sitemap'i güncelle
                header('Location: blogs.php');
                exit;
            } else {
                // Ekleme - Double submit önleme
                $stmt = $pdo->prepare("INSERT INTO blogs (title, content, meta_description, image, slug, reading_time, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$title, $content, $meta_description, $image, $slug, $reading_time, $active])) {
                    $_SESSION['flash_message'] = 'success|Blog yazısı başarıyla eklendi.';
                    updateSitemap(); // Sitemap'i güncelle
                    
                    // ÖNEMLI: Hemen redirect et ve çık
                    header('Location: blogs.php');
                    exit();
                } else {
                    $message = 'error|Blog yazısı eklenirken bir hata oluştu.';
                }
            }
        } else if (!$title || !$content) {
            $message = 'error|Başlık ve içerik alanları zorunludur!';
        }
    }
}

// Düzenleme için veri çek
$edit_blog = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $edit_blog = $stmt->fetch();
}

// Tüm blogları listele
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $edit_blog ? 'Blog Düzenle' : 'Yeni Blog Ekle'; ?></h2>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($edit_blog): ?>
            <input type="hidden" name="id" value="<?php echo $edit_blog['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label class="form-label">Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_blog ? clean($edit_blog['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Slug (URL) <small>(Boş bırakılırsa otomatik oluşturulur)</small></label>
            <input type="text" name="slug" class="form-control" value="<?php echo $edit_blog ? clean($edit_blog['slug']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Meta Açıklama (SEO)</label>
            <textarea name="meta_description" class="form-control" rows="2" maxlength="160"><?php echo $edit_blog ? clean($edit_blog['meta_description']) : ''; ?></textarea>
            <small style="color: var(--text-muted);">Google'da görünecek açıklama (max 160 karakter)</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">İçerik *</label>
            <textarea name="content" class="form-control" rows="15" required><?php echo $edit_blog ? clean($edit_blog['content']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Öne Çıkan Görsel <?php echo $edit_blog ? '' : '*'; ?></label>
            <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_blog ? '' : 'required'; ?>>
            <?php if ($edit_blog && $edit_blog['image']): ?>
                <img src="/assets/<?php echo clean($edit_blog['image']); ?>?v=<?php echo time(); ?>" style="max-width: 300px; margin-top: 10px; border-radius: 5px;">
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_blog || $edit_blog['active']) ? 'checked' : ''; ?>>
                <span>Yayınla</span>
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php echo $edit_blog ? 'Güncelle' : 'Ekle'; ?>
        </button>
        <?php if ($edit_blog): ?>
            <a href="blogs.php" class="btn btn-sm">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Blog Yazıları</h2>
    </div>
    
    <?php if (count($blogs) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Başlık</th>
                    <th>Slug</th>
                    <th>Okuma</th>
                    <th>Görüntülenme</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogs as $blog): ?>
                <tr>
                    <td>
                        <?php if ($blog['image']): ?>
                            <img src="/assets/<?php echo clean($blog['image']); ?>?v=<?php echo time(); ?>" 
                                 alt="<?php echo clean($blog['title']); ?>" 
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-newspaper" style="color: #ccc; font-size: 24px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo clean($blog['title']); ?></td>
                    <td><small><?php echo clean($blog['slug']); ?></small></td>
                    <td><?php echo $blog['reading_time']; ?> dk</td>
                    <td><?php echo $blog['views']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($blog['created_at'])); ?></td>
                    <td>
                        <?php if ($blog['active']): ?>
                            <span class="badge badge-success">Yayında</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Taslak</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btn-group">
                            <a href="?edit=<?php echo $blog['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i>
                                <span>Düzenle</span>
                            </a>
                            <button onclick="openDeleteModal(<?php echo $blog['id']; ?>, '<?php echo addslashes(clean($blog['title'] ?? '')); ?>', 'blog')" 
                                    class="btn-delete-modern">
                                <i class="fas fa-trash-alt"></i>
                                <span>Sil</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center" style="padding: 20px; color: var(--text-muted);">Henüz blog yazısı eklenmemiş.</p>
    <?php endif; ?>
</div>

<!-- Modern Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal-overlay">
    <div class="delete-modal">
        <div class="delete-modal-header">
            <div class="delete-modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="delete-modal-title">
                <h3>Silme Onayı</h3>
                <p>Bu işlem geri alınamaz!</p>
            </div>
        </div>
        
        <div class="delete-modal-body">
            <div class="delete-modal-content">
                <h4>Silinecek Blog:</h4>
                <div class="delete-modal-item" id="deleteItemName"></div>
            </div>
            
            <div class="delete-modal-warning">
                <i class="fas fa-exclamation-circle"></i>
                <div class="delete-modal-warning-text">
                    <strong>Dikkat!</strong> Bu işlem kalıcıdır ve geri alınamaz. 
                    Blog yazısı ve ilgili tüm veriler tamamen kaybolacaktır.
                </div>
            </div>
            
            <div class="delete-modal-actions">
                <button onclick="closeDeleteModal()" class="modal-btn modal-btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>İptal</span>
                </button>
                <button onclick="confirmDelete()" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt"></i>
                    <span>Evet, Sil</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteId = null;
let deleteToken = '<?php echo $_SESSION['csrf_token']; ?>';

function openDeleteModal(id, itemName, type = 'blog') {
    deleteId = id;
    document.getElementById('deleteItemName').textContent = itemName;
    
    const modal = document.getElementById('deleteModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    deleteId = null;
}

function confirmDelete() {
    if (!deleteId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Siliniyor...</span>';
    
    window.location.href = `?delete=${deleteId}&token=${deleteToken}`;
}

// ESC tuşu ile modal'ı kapat
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('deleteModal');
        if (modal.classList.contains('active')) {
            closeDeleteModal();
        }
    }
});

// Modal overlay'e tıklanınca kapat
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

