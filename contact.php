<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$sent   = false;
$values = ['name' => '', 'email' => '', 'company' => '', 'message' => ''];

// Pre-fill email from the homepage "Get Started" lead forms (?email=)
if (isset($_GET['email'])) {
    $values['email'] = trim((string) $_GET['email']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $values['name']    = trim((string) ($_POST['name'] ?? ''));
        $values['email']   = trim((string) ($_POST['email'] ?? ''));
        $values['company'] = trim((string) ($_POST['company'] ?? ''));
        $values['message'] = trim((string) ($_POST['message'] ?? ''));
        $source            = ($_POST['source'] ?? '') === 'lead' ? 'lead' : 'contact';

        if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($source === 'contact' && $values['message'] === '') {
            $errors[] = 'Please tell us how we can help.';
        }

        if (!$errors) {
            $stmt = db()->prepare(
                'INSERT INTO messages (name, email, company, message, source) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                mb_substr($values['name'], 0, 160),
                mb_substr($values['email'], 0, 190),
                mb_substr($values['company'], 0, 190),
                $values['message'] !== '' ? $values['message'] : '(Lead — requested consultation)',
                $source,
            ]);

            // Best-effort email notification (won't block on failure)
            $to   = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : setting('site.email', 'hello@thewalkingbillboard.com');
            $subj = 'New ' . ($source === 'lead' ? 'lead' : 'inquiry') . ' from ' . ($values['name'] ?: $values['email']);
            $body = "Name: {$values['name']}\nEmail: {$values['email']}\nCompany: {$values['company']}\n\n{$values['message']}";
            $headers = 'From: website@' . (parse_url(setting('site.url', 'https://thewbillboard.com'), PHP_URL_HOST) ?: 'thewbillboard.com')
                     . "\r\nReply-To: {$values['email']}\r\nContent-Type: text/plain; charset=UTF-8";
            @mail($to, $subj, $body, $headers);

            redirect('/contact?sent=1');
        }
    }
}

if (isset($_GET['sent'])) {
    $sent = true;
}

$page_title = setting('contact.meta_title', 'Contact | The Walking Billboard — PR & Brand Solutions');
$page_desc  = setting('contact.meta_desc', 'Book a free brand consultation with The Walking Billboard in Port Harcourt.');
$active = 'contact';
include __DIR__ . '/includes/header.php';

$phone   = setting('site.phone', '+2348174623187');
$email   = setting('site.email', 'hello@thewalkingbillboard.com');
$address = setting('site.address', 'Port Harcourt, Rivers State · Nigeria');
?>

<section class="contact-section" id="contact" style="padding-top:130px">
  <div class="contact-left fade-in">
    <span class="contact-arrow">↗</span>
    <h2 class="contact-heading">LET'S<br>TALK</h2>
    <p class="contact-subtext"><?= e(setting('contact.subtext', "Book a free consultation and find out exactly what TWB would do for your brand. Tell us what's working, what isn't, and what you want next — we'll respond with clear next steps.")) ?></p>

    <div class="contact-details" style="margin-top:0">
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

    <div class="contact-socials" style="margin-top:2rem">
      <?php include __DIR__ . '/includes/social-icons.php'; ?>
    </div>
  </div>

  <div class="contact-right fade-in">
    <span class="contact-form-label">Get a Free Consultation</span>

    <?php if ($sent): ?>
      <div class="form-banner success">✓ Thank you — your message has been received. We'll be in touch shortly.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="form-banner error"><?= e(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" action="/contact" class="contact-form-fields">
      <?= csrf_field() ?>
      <div class="contact-form-grid">
        <div>
          <label class="field-label" for="name">Name</label>
          <input class="field-input" id="name" name="name" type="text" value="<?= e($values['name']) ?>" placeholder="Your name">
        </div>
        <div>
          <label class="field-label" for="email">Email</label>
          <input class="field-input" id="email" name="email" type="email" required value="<?= e($values['email']) ?>" placeholder="your@email.com">
        </div>
      </div>
      <div>
        <label class="field-label" for="company">Brand / Company</label>
        <input class="field-input" id="company" name="company" type="text" value="<?= e($values['company']) ?>" placeholder="Brand name">
      </div>
      <div>
        <label class="field-label" for="message">How can we help?</label>
        <textarea class="field-textarea" id="message" name="message" required placeholder="Tell us about your goals, audience, timeline, and current challenges."><?= e($values['message']) ?></textarea>
      </div>
      <button class="btn-pill-blue" type="submit" style="align-self:flex-start;margin-top:0.25rem">Send Inquiry <span>→</span></button>
    </form>
  </div>
</section>

<div class="ticker-wrap">
  <div class="ticker-inner">
    <?php
    $t = ['Free Brand Consultation','Worth ₦50,000','Response Within 24 Hours','No Generic Pitch','Port Harcourt, Nigeria'];
    for ($r = 0; $r < 2; $r++) { foreach ($t as $x) { echo '<span class="ticker-item">' . e($x) . '</span><span class="ticker-sep">✦</span>'; } }
    ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
