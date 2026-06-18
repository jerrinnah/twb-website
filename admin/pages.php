<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

/**
 * Editable page-content fields, grouped by page.
 * type: text | textarea | html   (html = markup allowed, output raw)
 * Defaults mirror what the public pages fall back to.
 */
$groups = [
    'Home page' => [
        ['home.hero_eyebrow', 'Hero eyebrow', 'text', 'PR & Brand Solutions · Port Harcourt'],
        ['home.hero_headline', 'Hero headline', 'html', 'Turn Your Brand<br>Into a Story<br><em>Worth Talking About.</em>'],
        ['home.hero_subtext', 'Hero subtext', 'textarea', "Guiding your brand's journey through storytelling, strategy, and visibility that converts to real revenue."],
        ['home.badge_top', 'Floating badge — top', 'text', '1,000+ Brands Served'],
        ['home.badge_bottom', 'Floating badge — bottom', 'text', 'Story-first PR'],
        ['home.badge_mid', 'Floating badge — middle', 'text', 'Port Harcourt, Nigeria'],
        ['home.problems_heading', 'Problems heading', 'text', 'Are You Running Into These Problems?'],
        ['home.problem1_title', 'Problem 1 title', 'text', 'Invisible Brand?'],
        ['home.problem1_text', 'Problem 1 text', 'textarea', "You have a great product but no one knows about it. Visibility without strategy is just noise — and noise doesn't convert."],
        ['home.problem2_title', 'Problem 2 title', 'text', 'Agency Dependency?'],
        ['home.problem2_text', 'Problem 2 text', 'textarea', 'You spend money on agencies but never understand what was done or why. You stay dependent. TWB exists to change exactly that.'],
        ['home.problem3_title', 'Problem 3 title', 'text', "Leads That Don't Close?"],
        ['home.problem3_text', 'Problem 3 text', 'textarea', "You have visibility but it isn't translating into customers. The gap between attention and revenue needs a system, not more posts."],
        ['home.cta_text', 'CTA banner text', 'text', 'Free Brand Consultation'],
        ['home.cta_note', 'CTA banner note', 'text', 'worth ₦50,000'],
        ['home.approach_heading', 'Approach heading', 'textarea', 'We do the work. We show you exactly how it was done.'],
        ['home.approach_body', 'Approach body', 'html', 'Most agencies keep their process a black box. Clients stay dependent, pay high, and learn nothing. <strong>TWB fixes that.</strong> Every engagement ends with the client knowing more than when they arrived — about their brand, their audience, and their own growth system.'],
        ['home.contact_subtext', 'Contact section subtext', 'textarea', 'Book a discovery call and find out exactly what TWB would do for your brand — no vague proposals, no generic pitch.'],
    ],
    'About page' => [
        ['about.eyebrow', 'Eyebrow', 'text', 'Why We Exist'],
        ['about.header_title', 'Header title', 'html', 'We help brands become <em>impossible to ignore.</em>'],
        ['about.header_text', 'Header text', 'textarea', 'The Walking Billboard is a PR and brand strategy studio built for businesses ready to move from visibility to revenue — combining storytelling, public relations, audience insight, and execution so brands grow with confidence.'],
        ['about.mission_text', 'Mission text', 'html', 'Most agencies keep their process a black box — clients pay high, stay dependent, and learn nothing. <strong>We exist to change exactly that.</strong> We do the work, deliver real results, and show you precisely how it was done, so your brand walks away stronger and more self-sufficient than before.'],
        ['about.stat1_num', 'Stat 1 number', 'text', '10+'],
        ['about.stat1_label', 'Stat 1 label', 'text', 'Years across communications and brand growth'],
        ['about.stat2_num', 'Stat 2 number', 'text', '1,000+'],
        ['about.stat2_label', 'Stat 2 label', 'text', 'Brands and teams supported through campaigns'],
        ['about.stat3_num', 'Stat 3 number', 'text', '85%'],
        ['about.stat3_label', 'Stat 3 label', 'text', 'Of clients see better visibility within 90 days'],
    ],
    'Services page' => [
        ['services.eyebrow', 'Eyebrow', 'text', 'What We Do'],
        ['services.header_title', 'Header title', 'html', 'Services built to increase <em>visibility and sales.</em>'],
        ['services.header_text', 'Header text', 'textarea', 'From brand positioning to event execution, we handle the parts of growth that get lost in busy schedules and disconnected teams — and we show you exactly how it was done.'],
    ],
    'Blog page' => [
        ['blog.eyebrow', 'Eyebrow', 'text', 'The Journal'],
        ['blog.header_title', 'Header title', 'html', 'Brand insights worth <em>reading.</em>'],
        ['blog.header_text', 'Header text', 'textarea', 'Notes on PR, brand storytelling, and turning visibility into revenue — straight from the TWB team.'],
    ],
    'Contact page' => [
        ['contact.subtext', 'Intro subtext', 'textarea', "Book a free consultation and find out exactly what TWB would do for your brand. Tell us what's working, what isn't, and what you want next — we'll respond with clear next steps."],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    foreach ($groups as $fields) {
        foreach ($fields as [$key, , , ]) {
            if (array_key_exists($key, $_POST)) {
                set_setting($key, trim((string) $_POST[$key]));
            }
        }
    }
    flash('Page content saved.');
    redirect('/admin/pages.php');
}

$admin_title = 'Page Content';
$admin_active = 'pages';
include __DIR__ . '/../includes/admin-header.php';
?>

<form method="post" action="/admin/pages.php">
  <?= csrf_field() ?>
  <div class="panel">
    <p class="muted" style="margin-bottom:0.5rem">Edit the text on your main pages. Leave a field blank to restore its original wording. Fields marked “HTML allowed” accept tags like <code>&lt;br&gt;</code>, <code>&lt;em&gt;</code>, <code>&lt;strong&gt;</code>.</p>
  </div>

  <?php foreach ($groups as $groupName => $fields): ?>
    <div class="panel">
      <div class="field-group-title" style="margin-top:0"><?= e($groupName) ?></div>
      <?php foreach ($fields as [$key, $label, $type, $default]):
        $val = setting($key, $default);
      ?>
        <div class="field">
          <label for="<?= e($key) ?>"><?= e($label) ?><?php if ($type==='html'): ?> <span class="muted" style="font-weight:400">· HTML allowed</span><?php endif; ?></label>
          <?php if ($type === 'text'): ?>
            <input class="input" id="<?= e($key) ?>" name="<?= e($key) ?>" type="text" value="<?= e($val) ?>">
          <?php else: ?>
            <textarea class="textarea" id="<?= e($key) ?>" name="<?= e($key) ?>"><?= e($val) ?></textarea>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Save all changes</button>
    <a class="btn btn-ghost" href="/" target="_blank" rel="noopener">View site ↗</a>
  </div>
</form>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
