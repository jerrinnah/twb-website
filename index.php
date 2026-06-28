<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = setting('home.meta_title', 'The Walking Billboard — PR & Brand Solutions | Port Harcourt');
$page_desc  = setting('home.meta_desc', 'The Walking Billboard is a PR and brand strategy studio in Port Harcourt, Nigeria. We turn brands into stories worth talking about — storytelling, visibility, and conversion that drive real revenue.');
$active = 'home';
include __DIR__ . '/includes/header.php';

$phone   = setting('site.phone', '+2348174623187');
$email   = setting('site.email', 'enquiry@thewbillboard.com');
$address = setting('site.address', 'Port Harcourt, Rivers State · Nigeria');
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-left">
    <div class="hero-tag fade-in">
      <span class="hero-tag-dot"></span>
      <?= e(setting('home.hero_eyebrow', 'PR & Brand Solutions · Port Harcourt')) ?>
    </div>
    <h1 class="hero-headline fade-in">
      <?= setting('home.hero_headline', 'Turn Your Brand<br>Into a Story<br><em>Worth Talking About.</em>') ?>
    </h1>
    <p class="hero-sub fade-in">
      <?= e(setting('home.hero_subtext', "Guiding your brand's journey through storytelling, strategy, and visibility that converts to real revenue.")) ?>
    </p>
    <form class="hero-form fade-in" method="get" action="/contact">
      <div class="hero-input-row">
        <input class="hero-input" type="email" name="email" required placeholder="Enter your email to get started">
        <button class="hero-input-btn" type="submit">Get Started <span>→</span></button>
      </div>
    </form>
  </div>

  <div class="hero-right fade-in">
    <div class="hero-visual-bg"></div>
    <div class="hero-orb"><div class="hero-orb-inner"></div></div>
    <div class="hero-floating-tag tag-top">
      <span class="dot"></span> <?= e(setting('home.badge_top', '1,000+ Brands Served')) ?>
    </div>
    <div class="hero-floating-tag tag-bottom">
      <svg class="tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l2.4 7.2H22l-6 4.6 2.3 7.2L12 16.6 5.7 21l2.3-7.2-6-4.6h7.6z"/></svg>
      &nbsp;<?= e(setting('home.badge_bottom', 'Story-first PR')) ?>
    </div>
    <div class="hero-floating-tag tag-mid">
      <svg class="tag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
      &nbsp;<?= e(setting('home.badge_mid', 'Port Harcourt, Nigeria')) ?>
    </div>
  </div>
</section>

<!-- TICKER -->
<div class="ticker-wrap">
  <div class="ticker-inner">
    <?php
    $ticker = ['PR & Brand Storytelling','1,000+ Brands','Walking Billboard Activations','Influencer Management','TWB Brand Academy','Content Creation','Event Launch PR','Media Strategy'];
    for ($r = 0; $r < 2; $r++) {
        foreach ($ticker as $t) {
            echo '<span class="ticker-item">' . e($t) . '</span><span class="ticker-sep">✦</span>';
        }
    }
    ?>
  </div>
</div>

<!-- PROBLEMS -->
<section class="problems">
  <h2 class="problems-heading fade-in"><?= e(setting('home.problems_heading', 'Are You Running Into These Problems?')) ?></h2>
  <div class="problems-grid">
    <?php
    $problems = [
        ['Invisible Brand?', "You have a great product but no one knows about it. Visibility without strategy is just noise — and noise doesn't convert."],
        ['Agency Dependency?', 'You spend money on agencies but never understand what was done or why. You stay dependent. TWB exists to change exactly that.'],
        ["Leads That Don't Close?", 'You have visibility but it isn\'t translating into customers. The gap between attention and revenue needs a system, not more posts.'],
    ];
    foreach ($problems as $i => $p):
        $n = $i + 1;
        $title = setting("home.problem{$n}_title", $p[0]);
        $text  = setting("home.problem{$n}_text", $p[1]);
    ?>
    <div class="problem-card fade-in">
      <div class="problem-num">0<?= $n ?></div>
      <h3><?= e($title) ?></h3>
      <p><?= e($text) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SERVICES -->
<section class="services-section" id="services">
  <div class="services-ghost">WHAT WE DO</div>
  <p class="services-label fade-in">Our Services</p>
  <h2 class="services-heading fade-in">What We Can Do <strong>For You?</strong></h2>
  <div class="service-rows">
    <?php
    $services = [
        ['PR & Brand Storytelling', 'Full PR management — TWB acts as your publicist, building and protecting your brand narrative in the public space. Brand story development, press releases, crisis comms, and quarterly brand reviews.'],
        ['Walking Billboard Activations', 'Brand ambassadors deployed in streets, events, and venues — wearing your merch, carrying your message, generating the kind of presence a roadside sign never could. Minimum three ambassadors per activation, with real-time social documentation.'],
        ['Content Creation & Social Management', 'Monthly production of social media content built around a narrative arc, not random promotional posts. Three package tiers covering copy, captions, graphics, and short-form video. Full management option includes community engagement, DMs, and competitor monitoring.'],
        ['Influencer Matchmaking & Campaign Management', 'TWB identifies, vets, negotiates with, and manages influencers on your behalf — assessed on engagement rate, audience demographics, content quality, and past brand work. Full post-campaign reporting with reach, engagement, and qualitative brand assessment.'],
        ['Strategy & Brand Audit', 'A full brand strategy session or diagnostic audit that ends with a documented roadmap and prioritised action list. Not a mood board — a written plan that tells you what to do, in what order, and why. Delivered within five working days.'],
        ['Event PR & Lead-to-Sales Conversion', 'End-to-end PR for product launches and branded events — pre-event build-up, live coverage, post-event media recap. Plus a done-for-you sales conversion toolbox: DM scripts, follow-up sequences, objection handlers, and pricing conversation guides.'],
    ];
    foreach ($services as $s):
    ?>
    <div class="service-row fade-in">
      <div class="service-row-left">
        <div class="service-row-title"><?= e($s[0]) ?></div>
        <div class="service-row-body"><?= e($s[1]) ?></div>
      </div>
      <div class="service-row-arrow">+</div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- CTA BANNER -->
<div class="cta-banner">
  <div class="cta-banner-text"><?= e(setting('home.cta_text', 'Free Brand Consultation')) ?></div>
  <div class="cta-banner-right">
    <span class="cta-banner-note"><?= e(setting('home.cta_note', 'worth ₦50,000')) ?></span>
    <a href="/contact" class="btn-pill-white">Schedule a Free Call <span>↗</span></a>
  </div>
</div>

<!-- APPROACH -->
<section class="approach-section" id="approach">
  <div class="approach-left fade-in">
    <div class="approach-ghost-heading">OUR<br>APPROACH <span class="arrow-icon">↗</span></div>
    <div class="approach-visual"></div>
  </div>
  <div class="approach-right fade-in">
    <p class="approach-sublabel">How We Work</p>
    <h3 class="approach-subheading"><?= e(setting('home.approach_heading', 'We do the work. We show you exactly how it was done.')) ?></h3>
    <p class="approach-body"><?= setting('home.approach_body', 'Most agencies keep their process a black box. Clients stay dependent, pay high, and learn nothing. <strong>TWB fixes that.</strong> Every engagement ends with the client knowing more than when they arrived — about their brand, their audience, and their own growth system.') ?></p>
    <div class="approach-steps">
      <div class="approach-step"><div class="approach-step-num">1</div><div class="approach-step-text"><strong>Discovery &amp; Audit</strong>We assess your current brand position, messaging, and visibility gaps before recommending anything.</div></div>
      <div class="approach-step"><div class="approach-step-num">2</div><div class="approach-step-text"><strong>Strategy &amp; Roadmap</strong>A written plan — specific actions, in sequence, built around your budget and capacity. Not vague direction.</div></div>
      <div class="approach-step"><div class="approach-step-num">3</div><div class="approach-step-text"><strong>Execution &amp; Reporting</strong>We execute and report what actually happened — including what underperformed and why. No spin.</div></div>
    </div>
    <a href="/about" class="btn-pill-blue">Start Your Journey <span>→</span></a>
  </div>
</section>

<!-- CONTACT -->
<section class="contact-section" id="contact">
  <div class="contact-left fade-in">
    <span class="contact-arrow">↗</span>
    <h2 class="contact-heading">CONTACT<br>US TODAY</h2>
    <p class="contact-subtext"><?= e(setting('home.contact_subtext', 'Book a discovery call and find out exactly what TWB would do for your brand — no vague proposals, no generic pitch.')) ?></p>
    <div class="contact-socials">
      <?php include __DIR__ . '/includes/social-icons.php'; ?>
    </div>
  </div>
  <div class="contact-right fade-in">
    <span class="contact-form-label">Get a Free Consultation</span>
    <form method="get" action="/contact">
      <div class="contact-input-row">
        <input class="contact-input" type="email" name="email" required placeholder="Enter your email to get started">
        <button class="contact-btn" type="submit">Get Started <span style="font-size:0.85rem">→</span></button>
      </div>
    </form>
    <div class="contact-details">
      <div class="contact-detail">
        <div class="contact-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
        <a href="tel:<?= e($phone) ?>"><?= e(setting('site.phone_display', '+234 817 462 3187')) ?></a>
      </div>
      <div class="contact-detail">
        <div class="contact-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg></div>
        <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
      </div>
      <div class="contact-detail">
        <div class="contact-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <?= e($address) ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
