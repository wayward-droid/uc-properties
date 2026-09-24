<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require __DIR__ . '/entities.php';
$entity = input('entity');
if (!isset($entities[$entity])) {
    not_found();
}
$id = (int) input('id');
$record = row('SELECT * FROM `' . $entity . '` WHERE id=?', [$id]);
if (!$record) {
    not_found();
}
$error = '';
$title = 'Confirm deletion';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (input('confirm') !== 'DELETE') {
        $error = 'Type DELETE to confirm.';
    } else {
        try {
            run('DELETE FROM `' . $entity . '` WHERE id=?', [$id]);
            flash('The record was deleted.');
            go('admin/content.php?entity=' . $entity);
        } catch (PDOException $e) {
            $error = 'This record is in use. Reassign related records before deleting it.';
        };
    }
}
include __DIR__ . '/header.php';
?>
<div class="form-card">
    <h2>Delete <?= e( $record[$entities[$entity]['title']], ) ?>?</h2>
    <p>
        This cannot be undone. Deleting an estate also removes its plot options, media records, and
        design associations. Existing enquiries remain, with removed property links cleared.
        Uploaded files remain on disk until separately reviewed.
    </p>
    <?php if (
    $error
): ?>
    <div class="notice error" role="alert"><?= e( $error, ) ?></div>
    <?php endif; ?>
    <form method="post">
        <?= csrf() ?>
        <div class="field">
            <label for="confirm">Type DELETE to confirm</label>
            <input id="confirm" name="confirm" required pattern="DELETE" autocomplete="off" />
        </div>
        <div class="button-row mt-4">
            <button type="submit" class="btn btn-danger">Delete permanently</button>
            <a class="btn btn-outline" href="<?= e( url('admin/content.php?entity=' . $entity), ) ?>">Cancel</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/footer.php'; ?>
