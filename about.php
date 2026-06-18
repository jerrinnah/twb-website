<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = setting('about.meta_title', 'About | The Walking Billboard — PR & Brand Solutions');
$page_desc  = setting('about.meta_desc', 'The Walking Billboard is a PR and brand strategy studio built for businesses ready to move from visibility to revenue.');
$active = 'about';
include __DIR__ . '/includes/header.php';
?>

<header class="page-header">
  <div class="page-header-tag fade-in"><span class="dot"></span> <?= e(setting('about.eyebrow', 'Why We Exist')) ?></div>
  <h1 class="fade-in"><?= setting('about.header_title', 'We help brands become <em>impossible to ignore.</em>') ?></h1>
  <p class="fade-in"><?= e(setting('about.header_text', 'The Walking Billboard is a PR and brand strategy studio built for businesses ready to move from visibility to revenue — combining storytelling, public relations, audience insight, and execution so brands grow with confidence.')) ?></p>
</header>

<section class="about-section">
  <div class="about-grid">
    <div class="fade-in">
      <span class="eyebrow"><?= e(setting('about.mission_eyebrow', 'Our Mission')) ?></span>
      <p class="about-lead"><?= setting('about.mission_text', 'Most agencies keep their process a black box — clients pay high, stay dependent, and learn nothing. <strong>We exist to change exactly that.</strong> We do the work, deliver real results, and show you precisely how it was done, so your brand walks away stronger and more self-sufficient than before.') ?></p>
      <div class="stat-grid">
        <?php
        $stats = [
            ['10+', 'Years across communications and brand growth'],
            ['1,000+', 'Brands and teams supported through campaigns'],
            ['85%', 'Of clients see better visibility within 90 days'],
        ];
        foreach ($stats as $i => $st):
            $n = $i + 1;
        ?>
        <div class="stat-card">
          <strong><?= e(setting("about.stat{$n}_num", $st[0])) ?></strong>
          <p><?= e(setting("about.stat{$n}_label", $st[1])) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="about-visual fade-in"><div class="about-orb"></div></div>
  </div>
</section>

<section class="story-section">
  <h2 class="story-heading fade-in"><?= e(setting('about.story_heading', 'Our Story')) ?></h2>
  <div class="timeline-list">
    <?php
    $timeline = [
        ['2014', 'We began with a simple idea: brands deserve strategy that is both creative and measurable — not one or the other.'],
        ['2018', 'We expanded into live, street-level activations and brand storytelling campaigns that helped businesses connect with local audiences in Port Harcourt and beyond.'],
        ['2021', 'We led PR for major regional moments — from the Niger Delta Economic & Investment Summit to mission-driven foundations — proving transparent PR could be both credible and effective.'],
        ['2024', 'We built our full-service model: PR, content, influencer management, and lead-to-sales conversion under one roof for fast-growing Nigerian brands.'],
    ];
    foreach ($timeline as $i => $tl):
        $n = $i + 1;
    ?>
    <div class="timeline-item fade-in">
      <div class="timeline-year"><?= e(setting("about.timeline{$n}_year", $tl[0])) ?></div>
      <p><?= e(setting("about.timeline{$n}_text", $tl[1])) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="values-section">
  <h2 class="values-heading fade-in"><?= e(setting('about.values_heading', 'What We Value')) ?></h2>
  <div class="value-grid">
    <?php
    $values = [
        ['Clarity', 'Every strategy starts with a clear message, a defined audience, and a measurable goal. No fluff, no guesswork.'],
        ['Transparency', 'We show you what we are doing, why it matters, and what results to expect — including what underperformed and why.'],
        ['Momentum', 'We focus on practical execution that creates visibility, trust, and long-term growth — not vanity metrics.'],
    ];
    foreach ($values as $i => $v):
        $n = $i + 1;
    ?>
    <div class="value-card fade-in">
      <div class="value-num">0<?= $n ?></div>
      <h4><?= e(setting("about.value{$n}_title", $v[0])) ?></h4>
      <p><?= e(setting("about.value{$n}_text", $v[1])) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<div class="cta-banner">
  <div class="cta-banner-text"><?= e(setting('about.cta_text', 'Ready to build a brand people remember?')) ?></div>
  <div class="cta-banner-right">
    <a href="/contact" class="btn-pill-white">Book a Discovery Call <span>↗</span></a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
