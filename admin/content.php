<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require __DIR__ . '/entities.php';
$entity = input('entity', 'estates');
if (!isset($entities[$entity])) {
    not_found();
}
$spec = $entities[$entity];
$title = $spec['label'];
$total = (int) row('SELECT COUNT(*) AS n FROM `' . $entity . '`')['n'];
$page = max(1, min((int) input('page', '1'), max(1, (int) ceil($total / 25))));
$records = rows(
    'SELECT * FROM `' . $entity . '` ORDER BY id DESC LIMIT 25 OFFSET ' . ($page - 1) * 25,
);
include __DIR__ . '/header.php';
?>
<div class="admin-section-heading">
    <p><?= $total ?> record<?= $total === 1 ? '' : 's' ?></p>
    <a class="btn btn-small" href="<?= e( url('admin/edit.php?entity=' . $entity), ) ?>">Add new <?= icon('arrow') ?></a>
</div>
<?php if (
    $entity === 'compatibility'
): ?>
<p class="note">
    Approve only confirmed estate + plot + design associations. A matching plot size is not
    sufficient.
</p>
<?php endif; ?> <?php if ($records): ?>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th><?= $entity === 'compatibility' ? 'Association' : e( $spec['fields'][$spec['title']]['label'] ?? 'Record', ) ?></th>
                <th>Status / information</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (
    $records
    as $r
): ?>
            <tr>
                <td><?= (int) $r['id'] ?></td>
                <td>
                    <?php if ($entity === 'compatibility'):
    e(relation_choices('property_options')[$r['option_id']] ?? 'Plot removed') ?>
                    <br />
                    <small><?= e( relation_choices('prototypes')[$r['prototype_id']] ?? 'Design removed', ) ?></small>
                    <?php
else:
     ?>
                    <a href="<?= e(url('admin/edit.php?entity=' . $entity . '&id=' . $r['id'])) ?>"><?= e( $r[$spec['title']], ) ?></a>
                    <?php
endif; ?>
                </td>
                <td>
                    <?php
if (array_key_exists('published', $r)): ?>
                    <span class="status-tag"><?= $r['published'] ? 'Published' : 'Draft' ?></span>
                    <?php elseif (
    array_key_exists('approved', $r)
): ?>
                    <span class="status-tag"><?= $r['approved'] ? 'Approved' : 'Needs review' ?></span>
                    <?php elseif ($entity === 'media'):
    echo e(tag($r['kind']));
else:
    echo e($r['created_at']);
endif;
if ($entity === 'property_options'): ?>
                    <br />
                    <small><?= e( relation_choices('estates')[$r['estate_id']] ?? '', ) ?></small>
                    <?php endif;
?>
                </td>
                <td class="table-actions">
                    <a href="<?= e( url('admin/edit.php?entity=' . $entity . '&id=' . $r['id']), ) ?>">Edit</a>
                    <a class="danger-link" href="<?= e( url('admin/delete.php?entity=' . $entity . '&id=' . $r['id']), ) ?>">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <h2>Ready when you are.</h2>
    <p>Add the first record using the button above.</p>
</div>
<?php endif; ?> <?php if ($total > 25): ?>
<nav class="pagination" aria-label="Admin pages">
    <?php for (
    $p = 1;
    $p <= ceil($total / 25);
    $p++
): ?>
    <a href="<?= e(url('admin/content.php?entity=' . $entity . '&page=' . $p)) ?>" <?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?> <?php include __DIR__ . '/footer.php'; ?>
