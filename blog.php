<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/analytics.php';

// Analytics: Blog listesi sayfası ziyareti
trackPageView('blog_liste', $_SERVER['REQUEST_URI']);

$page_title = 'Blog';
$page_description = 'Sağlıklı beslenme, diyet ve yaşam hakkında bilgilendirici yazılar.';

// Pagination
$per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Toplam blog sayısı
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE active = 1");
$count_stmt->execute();
$total_blogs = $count_stmt->fetchColumn();

$pagination = paginate($total_blogs, $per_page, $current_page);

// Blogları getir
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE active = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$pagination['per_page'], $pagination['offset']]);
$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Blog List Section -->
<section class="section" style="padding-top: 80px;">
    <div class="container">
        <div class="section-title fade-in-up">
            <h2>Blog</h2>
            <p>Sağlıklı yaşam ve beslenme hakkında bilgilendirici yazılar</p>
        </div>
        
        <?php if (count($blogs) > 0): ?>
            <div class="grid grid-3">
                <?php foreach ($blogs as $blog): ?>
                <article class="card fade-in-up">
                    <?php if ($blog['image']): ?>
                        <img src="<?php echo imageUrl('assets/' . $blog['image']); ?>" 
                             alt="<?php echo clean($blog['title']); ?>" 
                             class="card-img" loading="eager">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>
                                <i class="far fa-calendar"></i> 
                                <?php echo formatDate($blog['created_at']); ?>
                            </span>
                            <span>
                                <i class="far fa-clock"></i> 
                                <?php echo $blog['reading_time']; ?> dk
                            </span>
                        </div>
                        <h3 class="card-title">
                            <a href="/blog/<?php echo $blog['id']; ?>/<?php echo slugify($blog['title']); ?>">
                                <?php echo clean($blog['title']); ?>
                            </a>
                        </h3>
                        <p class="card-text">
                            <?php echo truncate($blog['content'], 150); ?>
                        </p>
                        <a href="/blog/<?php echo $blog['id']; ?>/<?php echo slugify($blog['title']); ?>" 
                           class="btn btn-outline">
                            <i class="fas fa-arrow-right"></i> Devamını Oku
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination fade-in-up">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>"><i class="fas fa-chevron-left"></i> Önceki</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($current_page < $pagination['total_pages']): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>">Sonraki <i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center fade-in-up">
                <i class="fas fa-book-reader" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 20px; opacity: 0.5;"></i>
                <p style="font-size: 1.1rem; color: var(--text-gray);">Henüz blog yazısı bulunmamaktadır.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

