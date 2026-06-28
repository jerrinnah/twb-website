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
define('ADMIN_EMAIL', 'enquiry@thewbillboard.com');

// ── SMTP (recommended for reliable email delivery) ──────────
// Leave SMTP_HOST empty ('') to use PHP mail() instead. With these set,
// the contact form sends through your mailbox directly — far more reliable.
//   Port 465 → SMTP_SECURE 'ssl'   |   Port 587 → SMTP_SECURE 'tls'
define('SMTP_HOST', '');                          // e.g. 'mail.thewbillboard.com'
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');                     // 'ssl' | 'tls' | ''
define('SMTP_USER', 'enquiry@thewbillboard.com'); // full email address
define('SMTP_PASS', '');                          // mailbox password
define('MAIL_FROM', 'enquiry@thewbillboard.com');
define('MAIL_FROM_NAME', 'The Walking Billboard');

// ── Security ────────────────────────────────────────────────
// Change this to a long random string (used to harden sessions).
define('APP_SECRET', 'change-this-to-a-long-random-string');

// Set to true only while debugging; false in production.
define('APP_DEBUG', true);
