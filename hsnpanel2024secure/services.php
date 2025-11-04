<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Hizmetler';
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
        $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch();
        
        if ($service && $service['image']) {
            deleteFile($service['image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['flash_message'] = 'success|Hizmet başarıyla silindi.';
            header('Location: services.php');
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
        $order_position = intval($_POST['order_position'] ?? 0);
        
        // Eğer sıra girilmemişse, otomatik olarak en son sıra + 1 yap
        if ($order_position === 0 && $id === 0) {
            $stmt = $pdo->query("SELECT MAX(order_position) as max_order FROM services");
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
        
        if ($title && $description && !$message) {
            if ($id > 0) {
                // Güncelleme
                if ($image) {
                    // Eski resmi sil
                    $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    $old = $stmt->fetch();
                    if ($old && $old['image']) {
                        deleteFile($old['image']);
                    }
                    
                    $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, image = ?, order_position = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $image, $order_position, $active, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, order_position = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $order_position, $active, $id]);
                }
                $_SESSION['flash_message'] = 'success|Hizmet başarıyla güncellendi.';
                header('Location: services.php');
                exit;
            } else {
                // Ekleme - Double submit önleme
                $stmt = $pdo->prepare("INSERT INTO services (title, description, image, order_position, active) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$title, $description, $image, $order_position, $active])) {
                    $_SESSION['flash_message'] = 'success|Hizmet başarıyla eklendi.';
                    
                    // ÖNEMLI: Hemen redirect et ve çık
                    header('Location: services.php');
                    exit();
                } else {
                    $message = 'error|Hizmet eklenirken bir hata oluştu.';
                }
            }
        } else if (!$title || !$description) {
            $message = 'error|Başlık ve açıklama alanları zorunludur!';
        }
    }
}

// Düzenleme için veri çek
$edit_service = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $edit_service = $stmt->fetch();
}

// Tüm hizmetleri listele
$stmt = $pdo->query("SELECT * FROM services ORDER BY order_position ASC, id DESC");
$services = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $edit_service ? 'Hizmet Düzenle' : 'Yeni Hizmet Ekle'; ?></h2>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($edit_service): ?>
            <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label class="form-label">Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_service ? clean($edit_service['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Açıklama *</label>
            <textarea name="description" class="form-control" rows="5" required><?php echo $edit_service ? clean($edit_service['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                Resim <?php echo $edit_service ? '' : '*'; ?>
                <small style="color: #6c757d; font-weight: normal; margin-left: 8px;">
                    (Önerilen: 800x600px, Max: 2MB, Format: JPG, PNG, WebP)
                </small>
            </label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" <?php echo $edit_service ? '' : 'required'; ?>>
            <?php if ($edit_service && $edit_service['image']): ?>
                <div style="margin-top: 10px;">
                    <img src="/assets/<?php echo clean($edit_service['image']); ?>?v=<?php echo time(); ?>" 
                         alt="Mevcut Resim" 
                         style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                         id="currentImage">
                    <p style="color: #6c757d; font-size: 12px; margin-top: 5px;">Mevcut resim</p>
                </div>
            <?php endif; ?>
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
                   value="<?php echo $edit_service ? $edit_service['order_position'] : ''; ?>"
                   min="1"
                   placeholder="Örn: 1, 2, 3...">
            <small style="color: #6c757d; font-size: 12px; margin-top: 5px; display: block;">
                <i class="fas fa-info-circle"></i> 
                Küçük numara önce görünür (1, 2, 3...)
            </small>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_service || $edit_service['active']) ? 'checked' : ''; ?>>
                <span>Aktif</span>
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php echo $edit_service ? 'Güncelle' : 'Ekle'; ?>
        </button>
        <?php if ($edit_service): ?>
            <a href="services.php" class="btn btn-sm">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Hizmetler Listesi</h2>
    </div>
    
    <?php if (count($services) > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Resim</th>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Sıra</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td>
                        <?php if ($service['image']): ?>
                            <img src="/assets/<?php echo clean($service['image']); ?>?v=<?php echo time(); ?>" 
                                 alt="<?php echo clean($service['title']); ?>" 
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: #ccc; font-size: 24px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo clean($service['title'] ?? ''); ?></strong>
                    </td>
                    <td><?php echo truncate($service['description'] ?? '', 80); ?></td>
                    <td>
                        <span class="badge badge-info" style="font-size: 14px; padding: 6px 12px;">
                            <?php echo $service['order_position']; ?>
                        </span>
                    </td>
                    <td>
                        <button onclick="toggleStatus(<?php echo $service['id']; ?>, '<?php echo $service['active'] ? '0' : '1'; ?>')" 
                                class="badge badge-<?php echo $service['active'] ? 'success' : 'danger'; ?>" 
                                style="cursor: pointer; border: none; padding: 6px 12px;"
                                id="status-badge-<?php echo $service['id']; ?>">
                            <?php echo $service['active'] ? 'Aktif' : 'Pasif'; ?>
                        </button>
                    </td>
                    <td>
                        <div class="action-btn-group">
                            <button onclick="toggleStatus(<?php echo $service['id']; ?>, '<?php echo $service['active'] ? '0' : '1'; ?>')" 
                                    class="btn-toggle <?php echo $service['active'] ? 'active' : ''; ?>"
                                    id="toggle-btn-<?php echo $service['id']; ?>"
                                    title="<?php echo $service['active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                <i class="fas fa-<?php echo $service['active'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                <span><?php echo $service['active'] ? 'Aktif' : 'Pasif'; ?></span>
                            </button>
                            <a href="?edit=<?php echo $service['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i>
                                <span>Düzenle</span>
                            </a>
                            <button onclick="openDeleteModal(<?php echo $service['id']; ?>, '<?php echo addslashes(clean($service['title'] ?? '')); ?>', 'service')" 
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
    <p class="text-center" style="padding: 20px; color: var(--text-muted);">Henüz hizmet eklenmemiş.</p>
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
                <h4>Silinecek Öğe:</h4>
                <div class="delete-modal-item" id="deleteItemName"></div>
            </div>
            
            <div class="delete-modal-warning">
                <i class="fas fa-exclamation-circle"></i>
                <div class="delete-modal-warning-text">
                    <strong>Dikkat!</strong> Bu işlem kalıcıdır ve geri alınamaz. 
                    Silindikten sonra bu öğeye ait tüm veriler tamamen kaybolacaktır.
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
let deleteServiceId = null;
let deleteToken = '<?php echo $_SESSION['csrf_token']; ?>';

// Modern Modal Functions
function openDeleteModal(id, itemName, type = 'service') {
    deleteServiceId = id;
    document.getElementById('deleteItemName').textContent = itemName;
    
    const modal = document.getElementById('deleteModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    deleteServiceId = null;
}

function confirmDelete() {
    if (!deleteServiceId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Siliniyor...</span>';
    
    window.location.href = `?delete=${deleteServiceId}&token=${deleteToken}`;
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

// Hizmet durumunu toggle et (AJAX)
async function toggleStatus(id, newStatus) {
    const statusBadge = document.getElementById(`status-badge-${id}`);
    const toggleBtn = document.getElementById(`toggle-btn-${id}`);
    
    if (!toggleBtn) return;
    
    // Butonu devre dışı bırak
    const originalHTML = toggleBtn.innerHTML;
    toggleBtn.disabled = true;
    toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>İşleniyor...</span>';
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', newStatus);
        
        const response = await fetch('toggle_service.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Badge'i güncelle
            if (statusBadge) {
                if (result.status == 1) {
                    statusBadge.className = 'badge badge-success';
                    statusBadge.innerHTML = 'Aktif';
                } else {
                    statusBadge.className = 'badge badge-danger';
                    statusBadge.innerHTML = 'Pasif';
                }
            }
            
            // Modern toggle butonunu güncelle
            if (result.status == 1) {
                toggleBtn.className = 'btn-toggle active';
                toggleBtn.innerHTML = '<i class="fas fa-toggle-on"></i> <span>Aktif</span>';
                toggleBtn.setAttribute('onclick', `toggleStatus(${id}, '0')`);
                toggleBtn.title = 'Pasif Yap';
            } else {
                toggleBtn.className = 'btn-toggle';
                toggleBtn.innerHTML = '<i class="fas fa-toggle-off"></i> <span>Pasif</span>';
                toggleBtn.setAttribute('onclick', `toggleStatus(${id}, '1')`);
                toggleBtn.title = 'Aktif Yap';
            }
            toggleBtn.disabled = false;
            
            // Başarı mesajı
            const message = result.status == 1 ? 'Hizmet aktif edildi!' : 'Hizmet pasif edildi!';
            alert('✅ ' + message);
        } else {
            alert('❌ ' + result.message);
            toggleBtn.disabled = false;
            toggleBtn.innerHTML = originalHTML;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Bir hata oluştu!');
        toggleBtn.disabled = false;
        toggleBtn.innerHTML = originalHTML;
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

