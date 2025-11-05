<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($blog_id === 0) {
    header('Location: /blog');
    exit;
}

// Blog yazısını getir
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND active = 1");
$stmt->execute([$blog_id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: /blog');
    exit;
}

// Görüntülenme sayısını artır
$update_stmt = $pdo->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
$update_stmt->execute([$blog['id']]);

// Analytics: Blog detay ziyareti kaydet
trackPageView('blog_detay', $_SERVER['REQUEST_URI'], $blog['id']);

$page_title = $blog['title'];
$page_description = $blog['meta_description'] ?: truncate($blog['content'], 160);
$page_image = $blog['image'] ? 'assets/' . $blog['image'] : '';

// İlgili diğer yazılar
$related_stmt = $pdo->prepare("SELECT * FROM blogs WHERE id != ? AND active = 1 ORDER BY created_at DESC LIMIT 3");
$related_stmt->execute([$blog['id']]);
$related_blogs = $related_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Blog Detail Section -->
<article class="section" style="padding-top: 80px;">
    <div class="container" style="max-width: 900px;">
        <!-- Blog Header -->
        <header class="text-center mb-4 fade-in-up">
            <h1><?php echo clean($blog['title']); ?></h1>
            <div class="card-meta" style="justify-content: center; font-size: 16px;">
                <span>
                    <i class="far fa-calendar"></i> 
                    <?php echo formatDate($blog['created_at']); ?>
                </span>
                <span>
                    <i class="far fa-clock"></i> 
                    <?php echo $blog['reading_time']; ?> dakika okuma
                </span>
                <span>
                    <i class="far fa-eye"></i> 
                    <?php echo $blog['views']; ?> görüntülenme
                </span>
            </div>
        </header>
        
        <!-- Featured Image -->
        <?php if ($blog['image']): ?>
        <div class="mb-4 fade-in-up">
            <img src="<?php echo imageUrl('assets/' . $blog['image']); ?>" 
                 alt="<?php echo clean($blog['title']); ?>" 
                 style="width: 100%; border-radius: 15px; box-shadow: var(--shadow);">
        </div>
        <?php endif; ?>
        
        <!-- Blog Content -->
        <div class="blog-content fade-in-up" style="line-height: 1.8; font-size: 18px;">
            <?php echo nl2br(clean($blog['content'])); ?>
        </div>
        
        <!-- Share Buttons -->
        <div class="share-section fade-in-up">
            <h3><i class="fas fa-share-alt"></i> Bu yazıyı paylaşın</h3>
            <?php
            // Mevcut domain'i kullanarak tam URL oluştur (eski domain sorununu önler)
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $current_domain = $protocol . $_SERVER['HTTP_HOST'];
            $share_url = $current_domain . $_SERVER['REQUEST_URI'];
            ?>
            <div class="share-buttons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" 
                   target="_blank" class="share-btn facebook-btn">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode($blog['title']); ?>" 
                   target="_blank" class="share-btn twitter-btn">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' - ' . $share_url); ?>" 
                   target="_blank" class="share-btn whatsapp-btn">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Related Posts -->
<?php if (count($related_blogs) > 0): ?>
<section class="section bg-cream">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>İlgili Yazılar</h2>
            <p>Size önerebileceğim diğer yazılar</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($related_blogs as $related): ?>
            <div class="card fade-in-up">
                <?php if ($related['image']): ?>
                    <img src="/assets/<?php echo clean($related['image']); ?>" 
                         alt="<?php echo clean($related['title']); ?>" 
                         class="card-img" loading="lazy">
                <?php endif; ?>
                <div class="card-body">
                    <div class="card-meta">
                        <span>
                            <i class="far fa-calendar"></i> 
                            <?php echo formatDate($related['created_at']); ?>
                        </span>
                        <span>
                            <i class="far fa-clock"></i> 
                            <?php echo $related['reading_time']; ?> dk
                        </span>
                    </div>
                    <h3 class="card-title">
                        <a href="/blog/<?php echo $related['id']; ?>/<?php echo slugify($related['title']); ?>">
                            <?php echo clean($related['title']); ?>
                        </a>
                    </h3>
                    <a href="/blog/<?php echo $related['id']; ?>/<?php echo slugify($related['title']); ?>" 
                       class="btn btn-outline">
                        <i class="fas fa-arrow-right"></i> Devamını Oku
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.blog-content p {
    margin-bottom: 1.5rem;
}
.blog-content h2,
.blog-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: var(--primary-green);
}

.share-section {
    margin-top: 50px;
    padding: 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, var(--white) 100%);
    border-radius: 15px;
    text-align: center;
}

.share-section h3 {
    color: var(--text-dark);
    font-size: 1.3rem;
    margin-bottom: 25px;
    font-weight: 600;
}

.share-section h3 i {
    color: var(--primary-green);
    margin-right: 10px;
}

.share-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    border-radius: 25px;
    color: var(--white);
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.facebook-btn {
    background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
}

.twitter-btn {
    background: linear-gradient(135deg, #1da1f2 0%, #0c85d0 100%);
}

.whatsapp-btn {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
}

@media (max-width: 767px) {
    .share-section {
        padding: 25px 15px;
    }
    
    .share-buttons {
        flex-direction: column;
    }
    
    .share-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

