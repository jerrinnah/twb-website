-- ============================================================
-- The Walking Billboard — CMS database schema
-- MySQL / MariaDB (cPanel). Charset utf8mb4.
-- Run via install.php or import in phpMyAdmin.
-- ============================================================

SET NAMES utf8mb4;

-- ── Admin users ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(60)  NOT NULL,
  name          VARCHAR(120) NOT NULL DEFAULT '',
  email         VARCHAR(190) NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Blog categories ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name  VARCHAR(120) NOT NULL,
  slug  VARCHAR(140) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Blog posts ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS posts (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title          VARCHAR(255) NOT NULL,
  slug           VARCHAR(255) NOT NULL,
  excerpt        VARCHAR(500) NOT NULL DEFAULT '',
  body           MEDIUMTEXT   NOT NULL,
  featured_image VARCHAR(255) NOT NULL DEFAULT '',
  category_id    INT UNSIGNED NULL,
  author_id      INT UNSIGNED NULL,
  status         ENUM('draft','published') NOT NULL DEFAULT 'draft',
  views          INT UNSIGNED NOT NULL DEFAULT 0,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  published_at   DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_post_slug (slug),
  KEY idx_status_pub (status, published_at),
  KEY idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Key/value settings + editable page content ──────────────
CREATE TABLE IF NOT EXISTS settings (
  setting_key   VARCHAR(191) NOT NULL,
  setting_value MEDIUMTEXT   NOT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Contact form submissions / leads ────────────────────────
CREATE TABLE IF NOT EXISTS messages (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(160) NOT NULL DEFAULT '',
  email      VARCHAR(190) NOT NULL DEFAULT '',
  company    VARCHAR(190) NOT NULL DEFAULT '',
  message    TEXT         NOT NULL,
  source     VARCHAR(40)  NOT NULL DEFAULT 'contact',
  is_read    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_read (is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: default category ──────────────────────────────────
INSERT IGNORE INTO categories (id, name, slug) VALUES
  (1, 'News', 'news'),
  (2, 'Brand Strategy', 'brand-strategy'),
  (3, 'PR Tips', 'pr-tips');
