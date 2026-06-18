<?php
/** Front-end footer + scripts. */
require_once __DIR__ . '/functions.php';
$year = date('Y');
?>
<!-- FOOTER -->
<footer class="site-footer">
  <a href="/" class="footer-logo" aria-label="The Walking Billboard">
    <img class="brand-logo invert" src="<?= e(setting('site.logo', '/assets/logo.png')) ?>" alt="The Walking Billboard">
  </a>
  <ul class="footer-links">
    <li><a href="/services">Services</a></li>
    <li><a href="/about">About</a></li>
    <li><a href="/blog">Blog</a></li>
    <li><a href="/contact">Contact</a></li>
    <li><a href="/admin">Admin</a></li>
  </ul>
  <p class="footer-copy">© <span id="year"><?= $year ?></span> <?= e(setting('site.name', 'The Walking Billboard')) ?>. All rights reserved.</p>
</footer>

<script src="/script.js?v=<?= @filemtime(__DIR__ . '/../script.js') ?: '1' ?>"></script>
</body>
</html>
