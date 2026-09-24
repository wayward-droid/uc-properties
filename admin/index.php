<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
$title = 'A clear view of today';
$figures = [
    'New requests' => (int) row(
        "SELECT COUNT(*) AS n FROM requests WHERE status='New' AND is_demo=0",
    )['n'],
    'Inspection requests' => (int) row(
        "SELECT COUNT(*) AS n FROM requests WHERE type='inspection' AND is_demo=0",
    )['n'],
    'Published estates' => (int) row('SELECT COUNT(*) AS n FROM estates WHERE published=1')['n'],
    'Approved design links' => (int) row(
        'SELECT COUNT(*) AS n FROM compatibility WHERE approved=1',
    )['n'],
];
$recent = rows(
    'SELECT r.*,e.name AS estate_name FROM requests r LEFT JOIN estates e ON e.id=r.estate_id WHERE r.is_demo=0 ORDER BY r.created_at DESC LIMIT 8',
);
$pending = (int) row('SELECT COUNT(*) AS n FROM compatibility WHERE approved=0')['n'];
include __DIR__ . '/header.php';
?>
<div class="stats-grid">
    <?php foreach ($figures as $label => $number): ?>
    <article>
        <p><?= e( $label, ) ?></p>
        <strong><?= $number ?></strong>
    </article>
    <?php endforeach; ?>
</div>
<?php if ($pending): ?>
<p class="note">
    <?= $pending ?> design association<?= $pending === 1 ? ' needs' : 's need' ?> review.
    <a href="<?= e( url('admin/content.php?entity=compatibility'), ) ?>">Review compatibility</a>
    before those designs appear for their plot options.
</p>
<?php endif; ?>
<div class="admin-section-heading">
    <h2>Recent conversations</h2>
    <a class="text-link" href="<?= e( url('admin/requests.php'), ) ?>">View all requests <?= icon('arrow') ?></a>
</div>
<?php if (
    $recent
): ?>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th>Request</th>
                <th>Estate</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (
    $recent
    as $r
): ?>
            <tr>
                <td><a href="<?= e(url('admin/requests.php?id=' . $r['id'])) ?>"><?= e( $r['reference'], ) ?></a></td>
                <td><?= e($r['name']) ?></td>
                <td><?= e(tag($r['type'])) ?></td>
                <td><?= e( $r['estate_name'] ?? 'General enquiry', ) ?></td>
                <td><span class="status-tag"><?= e( $r['status'], ) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <h2>Your next conversation starts here.</h2>
    <p>Real enquiries and inspection requests will appear here when visitors submit them.</p>
</div>
<?php endif; ?>
<div class="admin-quicklinks">
    <a class="btn" href="<?= e( url('admin/edit.php?entity=estates'), ) ?>">Add an estate <?= icon('arrow') ?></a>
    <a class="btn btn-outline" href="<?= e( url('admin/edit.php?entity=articles'), ) ?>">Add a project update</a>
</div>
<?php include __DIR__ . '/footer.php'; ?>
