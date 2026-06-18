# The Walking Billboard — CMS Setup Guide

A lightweight, self-hosted CMS (PHP 8 + MySQL) for managing the blog and key
page content. Runs on your existing cPanel hosting. No frameworks, no Composer.

---

## What you can manage

- **Blog posts** — create / edit / publish / delete, with a rich-text editor,
  featured images, inline image uploads, categories, drafts, and SEO excerpts.
- **Page content** — edit the headlines and text on Home, About, Services,
  Blog, and Contact pages.
- **Site settings** — phone, email, address, and social links (used site-wide).
- **Messages** — every contact-form submission and homepage signup lands in the
  admin inbox.

Admin lives at **`/admin`**. Public blog lives at **`/blog`**.

---

## One-time installation (do this once)

### 1. Create the MySQL database (cPanel)
1. cPanel → **MySQL® Databases**.
2. **Create New Database** → e.g. `cms` (full name becomes `thewbillboard_cms`).
3. **Add New User** → create a user + strong password.
4. **Add User To Database** → grant **ALL PRIVILEGES**.
5. Note the final **database name**, **username**, and **password**.

### 2. Add your config file
On the server, copy `includes/config.sample.php` → `includes/config.php`
(File Manager → copy → rename), then **Edit** it and fill in:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'thewbillboard_cms');   // exact name from step 1
define('DB_USER', 'thewbillboard_cms');   // exact user from step 1
define('DB_PASS', 'your-real-password');
define('SITE_URL', 'https://thewbillboard.com');
define('ADMIN_EMAIL', 'hello@thewalkingbillboard.com');
define('APP_SECRET', 'paste-a-long-random-string-here');
define('APP_DEBUG', false);
```

> `config.php` is gitignored, so your password is never committed and is **not
> overwritten when you deploy**.

### 3. Run the installer
Visit **`https://thewbillboard.com/install.php`** → fill in your name, a
username, and a password (min 8 chars) → **Install**.
It creates all database tables and your admin account.

### 4. Delete the installer
Important: delete **`install.php`** from the server afterwards (the installer
refuses to run again once an admin exists, but remove it anyway).

### 5. Log in
Go to **`/admin`**, sign in, and you're running.

---

## Deploying updates (same git flow as before)

1. I push code to GitHub.
2. cPanel → **Git Version Control** → **Manage** → **Pull or Deploy** →
   **Update from Remote** → **Deploy HEAD Commit**.
3. The `.cpanel.yml` copies the app into `public_html`. It **never** overwrites
   `includes/config.php` or your uploaded images in `uploads/`.

---

## Requirements
- PHP 8.1+ — set in cPanel → **MultiPHP Manager**.
- MySQL/MariaDB.
- Apache `mod_rewrite` (standard on cPanel) for clean URLs.
- The `uploads/` folder must be writable by PHP (cPanel default `0755` is fine).

## Security notes
- Passwords are hashed (`password_hash`). All queries use prepared statements.
- All forms are CSRF-protected; admin pages require login.
- `includes/` is blocked from web access; `uploads/` cannot execute scripts.
- Still recommended: enable HTTPS (AutoSSL/Cloudflare) so admin logins are
  encrypted.

## Troubleshooting
- **"Configuration missing"** → you haven't created `includes/config.php` yet.
- **"We'll be right back"** on the public site → database credentials in
  `config.php` are wrong, or the DB is down.
- **Clean URLs / `/blog` 404** → `mod_rewrite` not active, or `.htaccess` wasn't
  deployed. Confirm `.htaccess` exists in `public_html`.
- **Images won't upload** → make `uploads/` writable (cPanel File Manager →
  Permissions → `0755`).
