<?php
/** Front-end footer + scripts. */
require_once __DIR__ . '/functions.php';
$year = date('Y');
?>
<!-- WALKING LEGS DIVIDER -->
<?php
// A pair of heart-footed legs strolling across the footer divider — a nod to
// our "Walking Billboard" service. Decorative only.
$twb_leg = '<svg viewBox="0 0 70 150" fill="currentColor" aria-hidden="true">'
  . '<path d="M31 4 C25 38 27 70 30 100 L46 100 C42 70 40 38 39 4 C37 1 33 1 31 4 Z"/>'
  . '<path d="M35 148 C18 132 12 118 12 108 C12 100 20 98 26 102 C30 104 33 108 35 112 '
  . 'C37 108 40 104 44 102 C50 98 58 100 58 108 C58 118 52 132 35 148 Z"/>'
  . '</svg>';
?>
<div class="footer-walk" aria-hidden="true">
  <div class="walker">
    <div class="walker-bob">
      <span class="leg leg-back"><?= $twb_leg ?></span>
      <span class="leg leg-front"><?= $twb_leg ?></span>
    </div>
  </div>
</div>

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
