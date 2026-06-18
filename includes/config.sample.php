<?php
/**
 * The Walking Billboard — configuration
 *
 * Copy this file to `config.php` (same folder) and fill in your
 * cPanel MySQL database details. config.php is gitignored so your
 * credentials never get committed or overwritten on deploy.
 */

// ── Database (create in cPanel → MySQL Databases) ───────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'thewbillboard_cms');   // e.g. cpaneluser_cms
define('DB_USER', 'thewbillboard_cms');   // e.g. cpaneluser_cms
define('DB_PASS', 'CHANGE_ME');

// ── Site ────────────────────────────────────────────────────
// Base URL with no trailing slash. Used for canonical links + emails.
define('SITE_URL', 'https://thewbillboard.com');

// Where contact form notifications are sent.
define('ADMIN_EMAIL', 'hello@thewalkingbillboard.com');

// ── Security ────────────────────────────────────────────────
// Change this to a long random string (used to harden sessions).
define('APP_SECRET', 'change-this-to-a-long-random-string');

// Set to true only while debugging; false in production.
define('APP_DEBUG', false);
