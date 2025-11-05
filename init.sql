-- Diyetisyen Hüsna Yılmaz Web Sitesi
-- Database: husnayilmaz_db
-- Coolify için init.sql - Tüm migration'lar entegre edilmiştir

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Admins tablosu
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısı (username: admin, password: admin123)
INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2y$10$EAt0wWR3bWv67W.f9Q.wc.31uw3BoYOxdyyxLxulhxLMuACBbGexe');

-- --------------------------------------------------------

-- Hizmetler tablosu
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `order_position` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Başarı Hikayeleri tablosu (cover_image kolonu entegre edildi)
CREATE TABLE `success_stories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `cover_image` varchar(255) NULL DEFAULT NULL,
  `content` text NOT NULL,
  `before_image` varchar(255) DEFAULT NULL,
  `after_image` varchar(255) DEFAULT NULL,
  `order_position` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Blog tablosu
CREATE TABLE `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `meta_description` varchar(160) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `reading_time` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `views` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Ayarlar tablosu
CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` longtext DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan ayarlar (telefon numarası güncellendi: +90 553 699 89 82)
INSERT INTO `settings` (`key`, `value`) VALUES
('video_url', 'https://www.youtube.com/embed/Ff0orcFVs6s'),
('about_text', 'Diyetisyen Hüsna Yılmaz olarak, sağlıklı yaşam yolculuğunuzda size rehberlik etmek için buradayım.'),
('working_hours', 'Pzt-Cmt: 09:00-17:30 | Pzr Kapalı'),
('instagram_url', 'https://www.instagram.com/dyt.husnayilmaz/'),
('whatsapp_number', '905536998982'),
('site_title', 'Diyetisyen Hüsna Yılmaz'),
('site_description', 'Alanya\'da profesyonel diyet ve beslenme danışmanlığı. Kişisel diyet programları, online danışmanlık.'),
('site_keywords', 'Alanya diyetisyen, Alanya sağlıklı beslenme, Alanya diyet danışmanı, beslenme uzmanı Alanya, diyet programı Alanya');

-- --------------------------------------------------------

-- İletişim mesajları tablosu (type kolonu entegre edildi)
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `type` ENUM('message', 'appointment') NOT NULL DEFAULT 'message',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Sertifikalar tablosu
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `order_position` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Analytics tablosu (migration entegre edildi)
CREATE TABLE IF NOT EXISTS `page_views` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(64) NOT NULL,
    `page_type` ENUM(
        'ana_sayfa', 
        'hakkimda', 
        'hizmetler', 
        'basari_hikayeleri', 
        'blog_liste', 
        'blog_detay', 
        'iletisim',
        'tool_vki', 
        'tool_bmh', 
        'tool_bel_kalca', 
        'tool_kalori', 
        'tool_karbonhidrat', 
        'tool_makro', 
        'tool_protein', 
        'tool_su', 
        'tool_yag', 
        'tool_ideal_kilo', 
        'tool_vucut_yag',
        -- Eski değerler (geriye uyumluluk için)
        'index',
        'blog'
    ) NOT NULL,
    `page_url` VARCHAR(255) NOT NULL,
    `blog_id` INT UNSIGNED NULL COMMENT 'Blog ID (sadece blog sayfaları için)',
    `user_agent` VARCHAR(255) NULL,
    `ip_address` VARCHAR(45) NULL,
    `referrer` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session_created` (`session_id`, `created_at`),
    INDEX `idx_page_type` (`page_type`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_blog_id` (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

