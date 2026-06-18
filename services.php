<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = setting('services.meta_title', 'Services | The Walking Billboard — PR & Brand Solutions');
$page_desc  = setting('services.meta_desc', 'PR & brand storytelling, walking billboard activations, content creation, influencer matchmaking, brand audits, and event PR.');
$active = 'services';
include __DIR__ . '/includes/header.php';
?>

<header class="page-header">
  <div class="page-header-tag fade-in"><span class="dot"></span> <?= e(setting('services.eyebrow', 'What We Do')) ?></div>
  <h1 class="fade-in"><?= setting('services.header_title', 'Services built to increase <em>visibility and sales.</em>') ?></h1>
  <p class="fade-in"><?= e(setting('services.header_text', 'From brand positioning to event execution, we handle the parts of growth that get lost in busy schedules and disconnected teams — and we show you exactly how it was done.')) ?></p>
</header>

<section class="services-detail">
  <div class="service-rows">
    <?php
    $services = [
        ['PR & Brand Storytelling', 'Full PR management — TWB acts as your publicist, building and protecting your brand narrative in the public space. Brand story development, press releases, crisis communications, and quarterly brand reviews across media, community, and public channels.'],
        ['Walking Billboard Activations', 'Real brand ambassadors deployed in streets, events, and venues — wearing your merch, carrying your message, generating presence a roadside sign never could. Minimum three ambassadors per activation, with real-time social documentation and reporting.'],
        ['Content Creation & Social Management', 'Monthly content built around a narrative arc, not random promotional posts. Three package tiers covering copy, captions, graphics, and short-form video. Full management adds community engagement, DMs, and competitor monitoring.'],
        ['Influencer Matchmaking & Campaign Management', 'We identify, vet, negotiate with, and manage influencers on your behalf — assessed on engagement rate, audience demographics, content quality, and past brand work. Full post-campaign reporting with reach, engagement, and qualitative brand assessment.'],
        ['Brand Audit & Strategy', 'A full brand strategy session or diagnostic audit ending with a documented roadmap and prioritised action list. Not a mood board — a written plan that tells you what to do, in what order, and why. Delivered within five working days.'],
        ['Event PR & Lead-to-Sales Conversion', 'End-to-end PR for launches and branded events — pre-event build-up, live coverage, post-event media recap. Plus a done-for-you sales conversion toolbox: DM scripts, follow-up sequences, objection handlers, and pricing conversation guides.'],
    ];
    foreach ($services as $i => $s):
    ?>
    <div class="service-row <?= $i === 0 ? 'open ' : '' ?>fade-in">
      <div class="service-row-left">
        <div class="service-row-title"><?= e($s[0]) ?></div>
        <div class="service-row-body"><?= e($s[1]) ?></div>
      </div>
      <div class="service-row-arrow">+</div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="process-section">
  <div style="text-align:center">
    <span class="eyebrow fade-in">How We Work</span>
    <h2 class="packages-heading fade-in" style="margin-bottom:0.5rem">A clear, transparent process</h2>
    <p class="packages-sub fade-in">Every engagement follows the same honest sequence — no black boxes.</p>
  </div>
  <div class="process-grid">
    <?php
    $process = [
        ['Discover', 'We assess your brand position, messaging, and visibility gaps before recommending anything.'],
        ['Strategise', 'A written roadmap — specific actions in sequence, built around your budget and capacity.'],
        ['Execute', 'We run the campaigns, activations, and content — and keep you in the loop throughout.'],
        ['Report', 'We show what actually happened, including what underperformed and why. No spin.'],
    ];
    foreach ($process as $i => $p):
    ?>
    <div class="process-card fade-in">
      <div class="process-num">0<?= $i + 1 ?></div>
      <h4><?= e($p[0]) ?></h4>
      <p><?= e($p[1]) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="packages-section">
  <h2 class="packages-heading fade-in">Flexible packages for every stage</h2>
  <p class="packages-sub fade-in">Pricing is tailored to scope — book a free consultation for a quote.</p>
  <div class="package-grid">
    <?php
    $packages = [
        ['Starter', 'Brand Visibility', 'Best for new brands building awareness and defining their message.', ['Brand messaging review', 'One campaign concept', 'Content calendar support', 'Monthly check-in call'], false],
        ['Most Popular', 'Growth Partner', 'Best for brands that need ongoing visibility, content, and campaign oversight.', ['PR & media strategy', 'Monthly content production', 'Influencer coordination', 'Quarterly brand review'], true],
        ['Scale', 'Launch System', 'Best for launches, events, and high-visibility campaigns that need precision.', ['Launch planning', 'Event PR coverage', 'Walking billboard activation', 'Post-event conversion support'], false],
    ];
    foreach ($packages as $p):
    ?>
    <div class="package-card <?= $p[4] ? 'featured ' : '' ?>fade-in">
      <div class="package-tier"><?= e($p[0]) ?></div>
      <h3 class="package-name"><?= e($p[1]) ?></h3>
      <p class="package-desc"><?= e($p[2]) ?></p>
      <ul><?php foreach ($p[3] as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul>
      <a href="/contact" class="btn-pill-blue">Get Started <span>→</span></a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<div class="cta-banner">
  <div class="cta-banner-text">Let's build the right growth plan for your brand.</div>
  <div class="cta-banner-right">
    <span class="cta-banner-note">Free consultation · worth ₦50,000</span>
    <a href="/contact" class="btn-pill-white">Start a Conversation <span>↗</span></a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
