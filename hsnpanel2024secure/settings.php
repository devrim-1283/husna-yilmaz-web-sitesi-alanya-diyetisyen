<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$page_title = 'Ayarlar';
$message = '';

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $updates = [
            'video_url' => trim($_POST['video_url'] ?? ''),
            'about_text' => trim($_POST['about_text'] ?? ''),
            'working_hours' => trim($_POST['working_hours'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'site_title' => trim($_POST['site_title'] ?? ''),
            'site_description' => trim($_POST['site_description'] ?? ''),
            'site_keywords' => trim($_POST['site_keywords'] ?? '')
        ];
        
        foreach ($updates as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
            $stmt->execute([$value, $key]);
        }
        
        $message = 'success|Ayarlar başarıyla güncellendi.';
        
        // Ayarları yeniden yükle
        $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <?php list($type, $text) = explode('|', $message); ?>
    <div class="alert alert-<?php echo $type; ?>"><?php echo $text; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Site Ayarları</h2>
    </div>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <h3 style="margin-bottom: 20px; color: var(--primary);">Genel Bilgiler</h3>
        
        <div class="form-group">
            <label class="form-label">Site Başlığı</label>
            <input type="text" name="site_title" class="form-control" value="<?php echo clean($settings['site_title'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Site Açıklaması (SEO)</label>
            <textarea name="site_description" class="form-control" rows="3"><?php echo clean($settings['site_description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Site Anahtar Kelimeleri (SEO)</label>
            <textarea name="site_keywords" class="form-control" rows="2"><?php echo clean($settings['site_keywords'] ?? ''); ?></textarea>
            <small style="color: var(--text-muted);">Virgülle ayırın</small>
        </div>
        
        <hr style="margin: 30px 0;">
        <h3 style="margin-bottom: 20px; color: var(--primary);">Hakkımda</h3>
        
        <div class="form-group">
            <label class="form-label">Hakkımda Metni</label>
            <textarea name="about_text" class="form-control" rows="6"><?php echo clean($settings['about_text'] ?? ''); ?></textarea>
        </div>
        
        <hr style="margin: 30px 0;">
        <h3 style="margin-bottom: 20px; color: var(--primary);">İletişim Bilgileri</h3>
        
        <div class="form-group">
            <label class="form-label">Çalışma Saatleri</label>
            <input type="text" name="working_hours" class="form-control" value="<?php echo clean($settings['working_hours'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">WhatsApp Numarası (905386912283 formatında)</label>
            <input type="text" name="whatsapp_number" class="form-control" value="<?php echo clean($settings['whatsapp_number'] ?? ''); ?>">
        </div>
        
        <hr style="margin: 30px 0;">
        <h3 style="margin-bottom: 20px; color: var(--primary);">Sosyal Medya</h3>
        
        <div class="form-group">
            <label class="form-label">Instagram URL</label>
            <input type="url" name="instagram_url" class="form-control" value="<?php echo clean($settings['instagram_url'] ?? ''); ?>">
        </div>
        
        <hr style="margin: 30px 0;">
        <h3 style="margin-bottom: 20px; color: var(--primary);">Video</h3>
        
        <div class="form-group">
            <label class="form-label">YouTube/Vimeo Embed URL</label>
            <input type="url" name="video_url" class="form-control" value="<?php echo clean($settings['video_url'] ?? ''); ?>">
            <small style="color: var(--text-muted);">Örnek: https://www.youtube.com/embed/VIDEO_ID</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

