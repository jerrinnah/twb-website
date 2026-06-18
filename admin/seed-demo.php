<?php
/**
 * Demo content seeder — adds (or removes) sample blog posts so you can
 * preview the blog/bento layout. Login-protected and fully reversible.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();

// category id helpers (seeded: 1=News, 2=Brand Strategy, 3=PR Tips)
$DEMO = [
    [
        'slug' => 'demo-visibility-without-strategy',
        'title' => 'Why Visibility Without Strategy Is Just Noise',
        'cat' => 2, 'img' => 'twb-strategy',
        'excerpt' => 'Going viral feels great for a week. But attention you can’t convert is a cost, not a win. Here’s how we turn reach into revenue.',
        'body' => '<p>Every brand wants to be seen. Few stop to ask <strong>seen by whom, and to what end</strong>. Visibility without a strategy behind it is just noise — expensive, forgettable, and impossible to repeat.</p><h2>Attention is a means, not the goal</h2><p>The brands that win treat every impression as the first step in a system: a clear message, a defined audience, and an action you actually want people to take.</p><ul><li>Define the one thing you want to be known for.</li><li>Map the journey from first glance to first sale.</li><li>Measure conversions, not just likes.</li></ul><p>That’s the difference between being popular and being profitable.</p>',
    ],
    [
        'slug' => 'demo-pr-moves-2026',
        'title' => '5 PR Moves Every Nigerian Brand Should Make in 2026',
        'cat' => 3, 'img' => 'twb-pr',
        'excerpt' => 'From owning your founder story to building a crisis playbook before you need one — the five moves that separate trusted brands from invisible ones.',
        'body' => '<p>The Nigerian market rewards brands that show up with clarity and consistency. These five moves are where we’d start.</p><h2>1. Own your founder story</h2><p>People trust people. Your origin is your most under-used asset.</p><h2>2. Build relationships before you need them</h2><p>Pitch journalists value, not favours.</p><h2>3. Document, don’t just create</h2><p>Every activation is content if you capture it.</p><h2>4. Prepare a crisis playbook</h2><p>Write it on a calm day.</p><h2>5. Measure what matters</h2><p>Reach is vanity; recall and revenue are sanity.</p>',
    ],
    [
        'slug' => 'demo-local-launch-regional-headlines',
        'title' => 'How We Turned a Local Launch Into Regional Headlines',
        'cat' => 1, 'img' => 'twb-launch',
        'excerpt' => 'A single product launch in Port Harcourt became a Niger-Delta-wide story. Here’s the exact playbook we ran — pre-buzz, live coverage, and the recap.',
        'body' => '<p>When a client came to us with a modest launch budget, the goal was simple: punch above our weight. We did it with sequencing, not spend.</p><h2>Pre-event build-up</h2><p>We seeded the story two weeks early with teasers and embargoed briefings.</p><h2>Live coverage</h2><p>Walking-billboard ambassadors plus real-time social turned attendees into broadcasters.</p><h2>The recap</h2><p>A tight media recap kept the story alive for a week after the doors closed.</p>',
    ],
    [
        'slug' => 'demo-walking-billboard-method',
        'title' => 'The Walking Billboard Method: Presence People Remember',
        'cat' => 2, 'img' => 'twb-method',
        'excerpt' => 'A roadside sign sits still and hopes. A walking billboard moves, talks, and creates moments. Here’s the thinking behind our signature approach.',
        'body' => '<p>Our name is our method. Static advertising waits to be noticed; <strong>presence</strong> goes out and earns attention.</p><p>Real brand ambassadors, deployed where your audience already is, generate the kind of word-of-mouth and documentation no billboard ever could — and every activation doubles as content.</p>',
    ],
    [
        'slug' => 'demo-influencer-partnerships-that-convert',
        'title' => 'Influencer Partnerships That Actually Convert',
        'cat' => 3, 'img' => 'twb-influencer',
        'excerpt' => 'Most influencer campaigns buy reach and hope. We brief for outcomes — and report on them. Here’s how to pick, vet, and manage creators properly.',
        'body' => '<p>The fastest way to waste money is to pay for follower counts. We assess creators on engagement quality, audience fit, and past brand work.</p><h2>Brief for outcomes</h2><p>Give creators a clear ask and creative freedom in equal measure.</p><h2>Report honestly</h2><p>Reach, engagement, and a qualitative read on brand lift — including what underperformed.</p>',
    ],
    [
        'slug' => 'demo-attention-to-revenue',
        'title' => 'From Attention to Revenue: Closing the Gap',
        'cat' => 2, 'img' => 'twb-revenue',
        'excerpt' => 'You have eyeballs. You don’t have sales. The missing piece is rarely more content — it’s a conversion system. Here’s the toolbox we install.',
        'body' => '<p>The gap between attention and revenue is where most brands quietly lose money. The fix is a system, not more posts.</p><ul><li>DM scripts that open conversations.</li><li>Follow-up sequences that don’t feel pushy.</li><li>Objection handlers and pricing guides.</li></ul><p>Install the system once and every campaign works harder.</p>',
    ],
    [
        'slug' => 'demo-crisis-comms-101',
        'title' => 'Crisis Comms 101: Protecting Your Brand Narrative',
        'cat' => 3, 'img' => 'twb-crisis',
        'excerpt' => 'The worst time to write your crisis plan is during a crisis. A calm-day guide to protecting the reputation you worked hard to build.',
        'body' => '<p>Reputation takes years to build and minutes to dent. A simple, pre-agreed plan keeps a bad day from becoming a bad year.</p><h2>Decide who speaks</h2><p>One voice, clear authority.</p><h2>Acknowledge fast, accurately</h2><p>Silence reads as guilt; spin reads as worse.</p><h2>Follow up</h2><p>Show what changed, not just what you said.</p>',
    ],
];

$slugs = array_column($DEMO, 'slug');
$errors = [];
$done = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $uid = current_user()['id'];
        $stmt = db()->prepare(
            'INSERT IGNORE INTO posts (title, slug, excerpt, body, featured_image, category_id, author_id, status, published_at)
             VALUES (?,?,?,?,?,?,?,"published",?)'
        );
        $n = 0;
        foreach ($DEMO as $i => $d) {
            $img = 'https://picsum.photos/seed/' . $d['img'] . '/1200/675';
            $pub = date('Y-m-d H:i:s', strtotime('-' . ($i * 4 + 1) . ' days'));
            $stmt->execute([$d['title'], $d['slug'], $d['excerpt'], $d['body'], $img, $d['cat'], $uid, $pub]);
            $n += $stmt->rowCount();
        }
        flash($n . ' demo post' . ($n === 1 ? '' : 's') . ' added. View the blog to see the layout.');
        redirect('/admin/seed-demo.php');
    }

    if ($action === 'remove') {
        $in = implode(',', array_fill(0, count($slugs), '?'));
        $del = db()->prepare("DELETE FROM posts WHERE slug IN ($in)");
        $del->execute($slugs);
        flash('Demo posts removed.');
        redirect('/admin/seed-demo.php');
    }
}

// how many demo posts currently exist
$in = implode(',', array_fill(0, count($slugs), '?'));
$existsStmt = db()->prepare("SELECT COUNT(*) FROM posts WHERE slug IN ($in)");
$existsStmt->execute($slugs);
$present = (int) $existsStmt->fetchColumn();

$admin_title = 'Demo Content';
$admin_active = 'posts';
include __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($errors): ?><div class="admin-flash error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>

<div class="panel">
  <h2>Demo blog content</h2>
  <p class="muted" style="margin-bottom:1.25rem">
    Add <?= count($DEMO) ?> sample PR/branding posts (with featured images) so you can preview the blog and bento layout.
    These are clearly marked as demos and can be removed with one click before you go live.
  </p>

  <?php if ($present > 0): ?>
    <p style="margin-bottom:1.25rem"><span class="tag published"><?= $present ?> demo post<?= $present === 1 ? '' : 's' ?> currently live</span></p>
  <?php endif; ?>

  <div class="actions">
    <form method="post" action="/admin/seed-demo.php" style="display:inline">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <button class="btn btn-primary" type="submit"><?= $present ? 'Re-add demo posts' : 'Add demo posts' ?></button>
    </form>
    <?php if ($present > 0): ?>
    <form method="post" action="/admin/seed-demo.php" style="display:inline" onsubmit="return confirm('Remove all demo posts?');">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="remove">
      <button class="btn btn-danger" type="submit">Remove demo posts</button>
    </form>
    <?php endif; ?>
    <a class="btn btn-ghost" href="/blog" target="_blank" rel="noopener">View blog ↗</a>
  </div>
</div>

<div class="panel">
  <p class="muted" style="font-size:0.82rem">Tip: the featured images are loaded from <code>picsum.photos</code> for the demo. When you write real posts, upload your own images in the post editor for the best result.</p>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
