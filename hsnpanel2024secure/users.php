<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Kullanıcılar';
$message = '';

// Silme işlemi
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verifyCsrfToken($_GET['token'])) {
        $id = intval($_GET['delete']);
        
        // Kendini silemesin
        if ($id != $_SESSION['admin_id']) {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'success|Kullanıcı başarıyla silindi.';
            }
        } else {
            $message = 'error|Kendi hesabınızı silemezsiniz!';
        }
    }
}

// Ekleme/Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if ($username) {
            if ($id > 0) {
                // Şifre güncelleme
                if ($password) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET username = ?, password_hash = ? WHERE id = ?");
                    $stmt->execute([$username, $password_hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
                    $stmt->execute([$username, $id]);
                }
                $message = 'success|Kullanıcı güncellendi.';
            } else {
                // Yeni kullanıcı
                if ($password) {
                    // Kullanıcı adı kontrolü
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = 'error|Bu kullanıcı adı zaten kullanılıyor!';
                    } else {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
                        $stmt->execute([$username, $password_hash]);
                        $message = 'success|Kullanıcı eklendi.';
                    }
                } else {
                    $message = 'error|Şifre boş olamaz!';
                }
            }
        }
    }
}

// Düzenleme için veri çek
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $edit_user = $stmt->fetch();
}

// Tüm kullanıcıları listele
$stmt = $pdo->query("SELECT * FROM admins ORDER BY id ASC");
$users = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo $edit_user ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle'; ?></h2>
    </div>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label class="form-label">Kullanıcı Adı *</label>
            <input type="text" name="username" class="form-control" value="<?php echo $edit_user ? clean($edit_user['username']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                Şifre <?php echo $edit_user ? '<small>(Değiştirmek istemiyorsanız boş bırakın)</small>' : '*'; ?>
            </label>
            <input type="password" name="password" class="form-control" <?php echo $edit_user ? '' : 'required'; ?>>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php echo $edit_user ? 'Güncelle' : 'Ekle'; ?>
        </button>
        <?php if ($edit_user): ?>
            <a href="users.php" class="btn btn-sm">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Admin Kullanıcıları</h2>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Oluşturulma</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <?php echo clean($user['username']); ?>
                        <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                            <span class="badge badge-success">Siz</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo formatDate($user['created_at']); ?></td>
                    <td>
                        <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                            <a href="?delete=<?php echo $user['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" 
                               class="btn btn-sm btn-danger btn-delete">Sil</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

