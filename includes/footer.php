<?php
/** Front-end footer + scripts. */
require_once __DIR__ . '/functions.php';
$year = date('Y');
?>
<!-- WALKING LEGS DIVIDER -->
<?php
// A pair of legs-in-shoes strolling across the footer divider — a nod to
// our "Walking Billboard" service. Traced from the brand sketch: thick tapered
// leg, ankle notch, chunky heart-style shoe. Decorative only.
$twb_leg = '<svg viewBox="0 0 300 580" fill="currentColor" aria-hidden="true">'
  . '<path d="M116 14 C100 130 116 268 140 358 L188 358 C202 268 192 138 182 16 '
  . 'C178 4 122 3 116 14 Z"/>'
  . '<path d="M158 566 C120 520 40 470 28 405 C20 360 60 330 100 345 '
  . 'C128 356 150 380 158 405 C166 380 188 356 216 345 C256 330 296 360 288 405 '
  . 'C276 470 196 520 158 566 Z"/>'
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
