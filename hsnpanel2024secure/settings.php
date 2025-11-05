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

<!-- Database Backup Section -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2 class="card-title">Veritabanı Yedekleme</h2>
    </div>
    <div class="card-body">
        <p style="margin-bottom: 20px; color: var(--text-gray);">
            Tüm veritabanınızı SQL dosyası olarak yedekleyebilirsiniz. Bu işlem tüm tabloları ve verilerini içerir.
        </p>
        
        <!-- Progress Bar Container -->
        <div id="backupProgressContainer" style="display: none; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span id="backupProgressText" style="font-weight: 600; color: var(--primary);">Yedekleme başlatılıyor...</span>
                <span id="backupProgressPercent" style="font-weight: 600; color: var(--primary);">0%</span>
            </div>
            <div style="width: 100%; height: 25px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div id="backupProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, var(--primary-green) 0%, var(--light-green) 100%); transition: width 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 600;">
                </div>
            </div>
        </div>
        
        <button id="backupBtn" class="btn btn-primary" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <i class="fas fa-database"></i>
            Veritabanını Yedekle
        </button>
    </div>
</div>

<!-- Assets Backup Section -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2 class="card-title">Assets Yedekleme</h2>
    </div>
    <div class="card-body">
        <p style="margin-bottom: 20px; color: var(--text-gray);">
            Tüm resim ve dosyalarınızı ZIP olarak yedekleyebilirsiniz. Bu işlem <code>assets/img</code> ve <code>assets/uploads</code> klasörlerini içerir.
        </p>
        
        <!-- Progress Bar Container -->
        <div id="assetsProgressContainer" style="display: none; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span id="assetsProgressText" style="font-weight: 600; color: var(--primary);">Yedekleme başlatılıyor...</span>
                <span id="assetsProgressPercent" style="font-weight: 600; color: var(--primary);">0%</span>
            </div>
            <div style="width: 100%; height: 25px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div id="assetsProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #007bff 0%, #0056b3 100%); transition: width 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 600;">
                </div>
            </div>
        </div>
        
        <button id="assetsBackupBtn" class="btn btn-primary" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
            <i class="fas fa-images"></i>
            Assets'leri Yedekle
        </button>
    </div>
</div>

<script>
document.getElementById('backupBtn').addEventListener('click', function() {
    const btn = this;
    const progressContainer = document.getElementById('backupProgressContainer');
    const progressBar = document.getElementById('backupProgressBar');
    const progressText = document.getElementById('backupProgressText');
    const progressPercent = document.getElementById('backupProgressPercent');
    
    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yedekleniyor...';
    
    // Show progress
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    progressText.textContent = 'Yedekleme başlatılıyor...';
    
    // CSRF token
    const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
    
    // Start backup
    const xhr = new XMLHttpRequest();
    let lastProgress = 0;
    
    xhr.open('GET', 'backup_database.php?token=' + encodeURIComponent(csrfToken), true);
    xhr.responseType = 'blob';
    
    xhr.onprogress = function(e) {
        if (e.lengthComputable && e.total > 0) {
            const percent = Math.min(Math.round((e.loaded / e.total) * 100), 100);
            if (percent > lastProgress) {
                lastProgress = percent;
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                
                // Progress text mesajları - simulated progress
                if (percent < 10) {
                    progressText.textContent = 'Veritabanı bağlantısı kuruluyor...';
                } else if (percent < 30) {
                    progressText.textContent = 'Tablo yapıları export ediliyor...';
                } else if (percent < 60) {
                    progressText.textContent = 'Veriler export ediliyor...';
                } else if (percent < 85) {
                    progressText.textContent = 'SQL dosyası oluşturuluyor...';
                } else if (percent < 95) {
                    progressText.textContent = 'Dosya hazırlanıyor...';
                } else {
                    progressText.textContent = 'Yedekleme tamamlandı! Dosya indiriliyor...';
                }
            }
        } else {
            // Length not computable - simulate progress
            if (lastProgress < 10) {
                lastProgress = 10;
                progressBar.style.width = '10%';
                progressPercent.textContent = '10%';
                progressText.textContent = 'Veritabanı bağlantısı kuruluyor...';
            } else if (lastProgress < 50) {
                lastProgress += 5;
                progressBar.style.width = lastProgress + '%';
                progressPercent.textContent = lastProgress + '%';
                progressText.textContent = 'Tablo yapıları export ediliyor...';
            } else if (lastProgress < 90) {
                lastProgress += 3;
                progressBar.style.width = lastProgress + '%';
                progressPercent.textContent = lastProgress + '%';
                progressText.textContent = 'Veriler export ediliyor...';
            }
        }
    };
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            progressText.textContent = 'Yedekleme başarıyla tamamlandı!';
            
            // Download file
            const blob = xhr.response;
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Get filename from Content-Disposition header
            const contentDisposition = xhr.getResponseHeader('Content-Disposition');
            let filename = 'backup_' + new Date().getTime() + '.sql';
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch) {
                    filename = filenameMatch[1];
                }
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Reset after 3 seconds
            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-database"></i> Veritabanını Yedekle';
                progressContainer.style.display = 'none';
            }, 3000);
        } else {
            progressText.textContent = 'Hata: Yedekleme başarısız oldu!';
            progressBar.style.background = '#dc3545';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-database"></i> Veritabanını Yedekle';
        }
    };
    
    xhr.onerror = function() {
        progressText.textContent = 'Hata: Bağlantı hatası!';
        progressBar.style.background = '#dc3545';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-database"></i> Veritabanını Yedekle';
    };
    
    xhr.send();
});

// Assets Backup Button
document.getElementById('assetsBackupBtn').addEventListener('click', function() {
    const btn = this;
    const progressContainer = document.getElementById('assetsProgressContainer');
    const progressBar = document.getElementById('assetsProgressBar');
    const progressText = document.getElementById('assetsProgressText');
    const progressPercent = document.getElementById('assetsProgressPercent');
    
    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yedekleniyor...';
    
    // Show progress
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    progressText.textContent = 'Yedekleme başlatılıyor...';
    
    // CSRF token
    const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
    
    // Start backup
    const xhr = new XMLHttpRequest();
    let lastProgress = 0;
    
    xhr.open('GET', 'backup_assets.php?token=' + encodeURIComponent(csrfToken), true);
    xhr.responseType = 'blob';
    
    xhr.onprogress = function(e) {
        if (e.lengthComputable && e.total > 0) {
            const percent = Math.min(Math.round((e.loaded / e.total) * 100), 100);
            if (percent > lastProgress) {
                lastProgress = percent;
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                
                // Progress text mesajları
                if (percent < 10) {
                    progressText.textContent = 'Dosyalar taranıyor...';
                } else if (percent < 30) {
                    progressText.textContent = 'Resimler arşivleniyor...';
                } else if (percent < 60) {
                    progressText.textContent = 'Upload klasörü arşivleniyor...';
                } else if (percent < 85) {
                    progressText.textContent = 'ZIP dosyası oluşturuluyor...';
                } else if (percent < 95) {
                    progressText.textContent = 'Dosya hazırlanıyor...';
                } else {
                    progressText.textContent = 'Yedekleme tamamlandı! Dosya indiriliyor...';
                }
            }
        } else {
            // Length not computable - simulate progress
            if (lastProgress < 10) {
                lastProgress = 10;
                progressBar.style.width = '10%';
                progressPercent.textContent = '10%';
                progressText.textContent = 'Dosyalar taranıyor...';
            } else if (lastProgress < 50) {
                lastProgress += 5;
                progressBar.style.width = lastProgress + '%';
                progressPercent.textContent = lastProgress + '%';
                progressText.textContent = 'Resimler arşivleniyor...';
            } else if (lastProgress < 90) {
                lastProgress += 3;
                progressBar.style.width = lastProgress + '%';
                progressPercent.textContent = lastProgress + '%';
                progressText.textContent = 'ZIP dosyası oluşturuluyor...';
            }
        }
    };
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            progressText.textContent = 'Yedekleme başarıyla tamamlandı!';
            
            // Download file
            const blob = xhr.response;
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Get filename from Content-Disposition header
            const contentDisposition = xhr.getResponseHeader('Content-Disposition');
            let filename = 'assets_backup_' + new Date().getTime() + '.zip';
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch) {
                    filename = filenameMatch[1];
                }
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Reset after 3 seconds
            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-images"></i> Assets\'leri Yedekle';
                progressContainer.style.display = 'none';
            }, 3000);
        } else {
            progressText.textContent = 'Hata: Yedekleme başarısız oldu!';
            progressBar.style.background = '#dc3545';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-images"></i> Assets\'leri Yedekle';
        }
    };
    
    xhr.onerror = function() {
        progressText.textContent = 'Hata: Bağlantı hatası!';
        progressBar.style.background = '#dc3545';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-images"></i> Assets\'leri Yedekle';
    };
    
    xhr.send();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

