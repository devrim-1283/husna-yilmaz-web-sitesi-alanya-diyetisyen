<?php
if (!isset($page_title)) $page_title = '';
if (!isset($page_description)) $page_description = '';
if (!isset($page_keywords)) $page_keywords = '';
if (!isset($page_image)) $page_image = '';

$meta = generateMetaTags($page_title, $page_description, $page_keywords, $page_image);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo clean($meta['title']); ?></title>
    <meta name="description" content="<?php echo clean($meta['description']); ?>">
    <meta name="keywords" content="<?php echo clean($meta['keywords']); ?>">
    <meta name="author" content="Diyetisyen Hüsna Yılmaz">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo clean($meta['url']); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo clean($meta['title']); ?>">
    <meta property="og:description" content="<?php echo clean($meta['description']); ?>">
    <meta property="og:image" content="<?php echo clean($meta['image']); ?>">
    <meta property="og:url" content="<?php echo clean($meta['url']); ?>">
    <meta property="og:site_name" content="Diyetisyen Hüsna Yılmaz">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo clean($meta['title']); ?>">
    <meta name="twitter:description" content="<?php echo clean($meta['description']); ?>">
    <meta name="twitter:image" content="<?php echo clean($meta['image']); ?>">
    <meta name="twitter:site" content="@devrimsoft">
    
    <!-- Favicon - Simple data URI for now -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo assetUrl('assets/css/style.css'); ?>">
    
    <!-- Schema.org JSON-LD - SEO Optimized -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Dietitian",
        "name": "Diyetisyen Hüsna Yılmaz",
        "alternateName": "Hüsna Yılmaz",
        "image": "<?php echo clean($meta['image']); ?>",
        "telephone": "+905536998982",
        "email": "destek@husnayilmaz.com",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Alanya",
            "addressRegion": "Antalya",
            "postalCode": "07400",
            "addressCountry": "TR",
            "streetAddress": "Alanya, Antalya"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "36.543475",
            "longitude": "32.000434"
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens": "09:00",
                "closes": "17:30"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Saturday",
                "opens": "09:00",
                "closes": "14:00"
            }
        ],
        "url": "<?php echo SITE_URL; ?>",
        "sameAs": [
            "<?php echo clean($settings['instagram_url'] ?? ''); ?>"
        ],
        "priceRange": "$$",
        "areaServed": {
            "@type": "City",
            "name": "Alanya"
        },
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Diyetisyen Hizmetleri",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Kişiye Özel Diyet Programı"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Online Danışmanlık"
                    }
                }
            ]
        }
    }
    </script>
</head>
<body>
    <!-- Top Mini Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-content">
                <div class="top-header-left">
                    <span class="top-header-item top-header-hours">
                        <i class="far fa-clock"></i>
                        <span class="text-content">Pzt-Cmt: 09:00-17:30 | Pzr Kapalı</span>
                    </span>
                    <a href="https://wa.me/<?php echo clean($settings['whatsapp_number'] ?? '905536998982'); ?>?text=Merhaba,%20%2540%2040%20indirim%20almak%20için%20randevu%20oluşturmak%20istiyorum" 
                       target="_blank" class="top-header-item top-header-promo">
                        <i class="fas fa-gift"></i>
                        <span class="text-content">Şimdi Randevu Oluşturun %40 İndirim</span>
                    </a>
                </div>
                <div class="top-header-right">
                    <a href="tel:+905536998982" class="top-header-item top-header-phone">
                        <i class="fas fa-phone"></i>
                        <span class="text-content">+90 553 699 89 82</span>
                    </a>
                    <a href="https://maps.app.goo.gl/A2TD94jUF7dr514F6" 
                       target="_blank" class="top-header-item top-header-location" aria-label="Konum">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="text-content">Alanya</span>
                    </a>
                    <a href="https://wa.me/<?php echo clean($settings['whatsapp_number'] ?? '905536998982'); ?>" 
                       target="_blank" class="social-icon" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="<?php echo clean($settings['instagram_url'] ?? '#'); ?>" 
                       target="_blank" class="social-icon" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <h1 class="logo-text">Diyetisyen<br><span>Hüsna Yılmaz</span></h1>
                    </a>
                </div>
                
                <nav class="main-nav" id="mainNav">
                    <ul class="nav-menu">
                        <li><a href="/about" class="<?php echo $current_page == 'about' ? 'active' : ''; ?>">
                            <i class="fas fa-user"></i> Hakkımda
                        </a></li>
                        <li><a href="/services" class="<?php echo $current_page == 'services' ? 'active' : ''; ?>">
                            <i class="fas fa-concierge-bell"></i> Hizmetlerim
                        </a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-tools"></i> Araçlar <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/vki">
                                    <i class="fas fa-calculator"></i> Vücut Kitle İndeksi
                                </a></li>
                                <li><a href="/bmh">
                                    <i class="fas fa-heartbeat"></i> Bazal Metabolizma Hızı
                                </a></li>
                                <li><a href="/bel-kalca-orani">
                                    <i class="fas fa-ruler"></i> Bel / Kalça Oranı
                                </a></li>
                                <li><a href="/gunluk-kalori">
                                    <i class="fas fa-fire"></i> Günlük Kalori İhtiyacı
                                </a></li>
                                <li><a href="/gunluk-karbonhidrat">
                                    <i class="fas fa-bread-slice"></i> Günlük Karbonhidrat İhtiyacı
                                </a></li>
                                <li><a href="/gunluk-makro">
                                    <i class="fas fa-chart-pie"></i> Günlük Makro Besin İhtiyacı
                                </a></li>
                                <li><a href="/gunluk-protein">
                                    <i class="fas fa-drumstick-bite"></i> Günlük Protein İhtiyacı
                                </a></li>
                                <li><a href="/gunluk-su">
                                    <i class="fas fa-tint"></i> Günlük Su İhtiyacı
                                </a></li>
                                <li><a href="/gunluk-yag">
                                    <i class="fas fa-oil-can"></i> Günlük Yağ İhtiyacı
                                </a></li>
                                <li><a href="/ideal-kilo">
                                    <i class="fas fa-weight"></i> İdeal Kilo Hesaplama
                                </a></li>
                                <li><a href="/vucut-yag-orani">
                                    <i class="fas fa-percentage"></i> Vücut Yağ Oranı
                                </a></li>
                            </ul>
                        </li>
                        <li><a href="/success-stories" class="<?php echo $current_page == 'success-stories' ? 'active' : ''; ?>">
                            <i class="fas fa-trophy"></i> Başarı Hikayeleri
                        </a></li>
                        <li><a href="/blog" class="<?php echo $current_page == 'blog' ? 'active' : ''; ?>">
                            <i class="fas fa-blog"></i> Blog
                        </a></li>
                        <li><a href="/contact" class="<?php echo $current_page == 'contact' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i> İletişim
                        </a></li>
                    </ul>
                </nav>

                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menüyü Aç/Kapat">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/<?php echo clean($settings['whatsapp_number'] ?? '905386912283'); ?>" 
       class="whatsapp-float" target="_blank" aria-label="WhatsApp ile İletişim">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Main Content -->
    <main class="main-content">

