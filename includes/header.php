<?php
/**
 * Front-end <head> + navigation.
 * Set $page_title, $page_desc, $active before including.
 */
require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? 'The Walking Billboard — PR & Brand Solutions';
$page_desc  = $page_desc  ?? setting('site.description', 'PR and brand strategy studio in Port Harcourt, Nigeria.');
$active     = $active ?? '';
$phone      = setting('site.phone', '+2348174623187');
$phone_disp = setting('site.phone_display', '(817) 462-3187');
$nav = [
    ['/',         'Home',     'home'],
    ['/services', 'Services', 'services'],
    ['/about',    'About',    'about'],
    ['/blog',     'Blog',     'blog'],
    ['/contact',  'Contact',  'contact'],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_desc) ?>">
<link rel="icon" href="<?= e(setting('site.logo', '/assets/logo.png')) ?>" type="image/png">
<link rel="apple-touch-icon" href="<?= e(setting('site.logo', '/assets/logo.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script>document.documentElement.classList.add('js');</script>
<link rel="stylesheet" href="/styles.css?v=<?= @filemtime(__DIR__ . '/../styles.css') ?: '1' ?>">
</head>
<body>

<!-- NAV -->
<nav>
  <a href="/" class="nav-logo" aria-label="The Walking Billboard — home">
    <img class="brand-logo" src="<?= e(setting('site.logo', '/assets/logo.png')) ?>" alt="The Walking Billboard">
  </a>
  <button class="nav-toggle" aria-label="Toggle navigation"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
  <ul class="nav-links">
    <?php foreach ($nav as [$href, $label, $key]): ?>
      <li><a href="<?= e($href) ?>"<?= $active === $key ? ' class="active"' : '' ?>><?= e($label) ?></a></li>
    <?php endforeach; ?>
    <li><a href="tel:<?= e($phone) ?>" class="nav-cta-mobile"><?= e($phone_disp) ?></a></li>
  </ul>
  <a href="tel:<?= e($phone) ?>" class="nav-cta"><?= e($phone_disp) ?></a>
</nav>
