<?php
/**
 * Email diagnostic — admin only.
 * Sends a test message and shows the full SMTP conversation so delivery
 * problems (connection, login, recipient, routing) are visible.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();

$ran = false;
$result = false;
$transcript = [];
$mode = '';

$to = setting('site.enquiry_email', defined('ADMIN_EMAIL') && ADMIN_EMAIL ? ADMIN_EMAIL : 'enquiry@thewbillboard.com');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $ran  = true;
    $subj = 'TWB email test · ' . date('Y-m-d H:i:s');
    $body = "This is a test message from the TWB admin email diagnostic.\nIf you can read this in the enquiry inbox, delivery works.";

    if (defined('SMTP_HOST') && SMTP_HOST !== '') {
        $mode = 'SMTP';
        $log = [];
        $result = smtp_send(
            (string) SMTP_HOST,
            defined('SMTP_PORT') ? (int) SMTP_PORT : 465,
            defined('SMTP_SECURE') ? (string) SMTP_SECURE : 'ssl',
            defined('SMTP_USER') ? (string) SMTP_USER : '',
            defined('SMTP_PASS') ? (string) SMTP_PASS : '',
            defined('MAIL_FROM') ? (string) MAIL_FROM : $to,
            defined('MAIL_FROM_NAME') ? (string) MAIL_FROM_NAME : 'The Walking Billboard',
            $to, $subj, $body,
            defined('MAIL_FROM') ? (string) MAIL_FROM : $to,
            $log
        );
        $transcript = $log;
    } else {
        $mode = 'PHP mail()';
        $result = send_mail($to, $subj, $body);
        $transcript[] = 'SMTP_HOST is not set, so PHP mail() was used.';
        $transcript[] = 'mail() returned: ' . ($result ? 'true (handed to server)' : 'false (server rejected it)');
    }
}

$cfg = [
    'SMTP_HOST'   => defined('SMTP_HOST') ? (SMTP_HOST !== '' ? SMTP_HOST : '(empty — using mail())') : '(not defined)',
    'SMTP_PORT'   => defined('SMTP_PORT') ? (string) SMTP_PORT : '(not defined)',
    'SMTP_SECURE' => defined('SMTP_SECURE') ? (string) SMTP_SECURE : '(not defined)',
    'SMTP_USER'   => defined('SMTP_USER') ? (string) SMTP_USER : '(not defined)',
    'SMTP_PASS'   => defined('SMTP_PASS') && SMTP_PASS !== '' ? str_repeat('•', strlen((string) SMTP_PASS)) . ' (set)' : '(empty)',
    'MAIL_FROM'   => defined('MAIL_FROM') ? (string) MAIL_FROM : '(not defined)',
    'Recipient'   => $to,
];

$admin_title = 'Email test';
$admin_active = 'settings';
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="panel">
  <h2>Email diagnostic</h2>
  <p class="muted" style="margin-bottom:1.25rem">Sends a test email to the enquiry inbox and shows exactly what the mail server says — so we can see <em>why</em> a message does or doesn't arrive.</p>

  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;margin-bottom:1.25rem">
    <?php foreach ($cfg as $k => $v): ?>
      <tr>
        <td style="padding:0.4rem 0.75rem 0.4rem 0;color:#888;white-space:nowrap;vertical-align:top"><?= e($k) ?></td>
        <td style="padding:0.4rem 0;font-family:monospace"><?= e($v) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <form method="post" action="/admin/mail-test.php">
    <?= csrf_field() ?>
    <div class="actions">
      <button class="btn btn-primary" type="submit">Send test email</button>
    </div>
  </form>
</div>

<?php if ($ran): ?>
  <div class="panel">
    <h2><?= $result ? '✓ Sent' : '✗ Failed' ?> <span class="muted" style="font-weight:400">(via <?= e($mode) ?>)</span></h2>
    <?php if ($result): ?>
      <p class="muted">The server accepted the message. If it's still not in the inbox, check the <strong>spam folder</strong> and cPanel → Email → <strong>Track Delivery</strong> for where it went.</p>
    <?php else: ?>
      <p class="muted">The send failed. The conversation below shows where it stopped — the last <code>S:</code> line is the server's reason.</p>
    <?php endif; ?>
    <?php if ($transcript): ?>
      <pre style="background:#0c0c0c;color:#cfcfcf;padding:1rem;border-radius:8px;overflow:auto;font-size:0.8rem;line-height:1.55;white-space:pre-wrap"><?php
        foreach ($transcript as $line) { echo e($line) . "\n"; }
      ?></pre>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php $tail = mail_log_tail(25); ?>
<div class="panel">
  <h2>Recent send log</h2>
  <p class="muted" style="margin-bottom:1rem">The outcome of every email the site has tried to send — including real contact-form submissions. <strong>OK</strong> means the mail server accepted it (if it's then missing, check the spam folder). <strong>FAIL</strong> means it was rejected — the reason is on the right.</p>
  <?php if (!$tail): ?>
    <p class="muted">No sends logged yet. Submit the contact form once, then refresh this page.</p>
  <?php else: ?>
    <pre style="background:#0c0c0c;color:#cfcfcf;padding:1rem;border-radius:8px;overflow:auto;font-size:0.78rem;line-height:1.6;white-space:pre"><?php
      foreach (array_reverse($tail) as $line) { echo e($line) . "\n"; }
    ?></pre>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
