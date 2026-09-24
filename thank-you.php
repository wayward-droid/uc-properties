<?php require __DIR__ . '/includes/bootstrap.php';
if (empty($_SESSION['last_request'])) {
    go('contact.php');
}
$request = $_SESSION['last_request'];
$title = 'We have received your request';
include __DIR__ . '/includes/header.php';
?>
<main id="main" class="container section reading">
    <p class="eyebrow">THANK YOU FOR GETTING IN TOUCH</p>
    <h1>
        Your next chapter
        <br />
        is taking shape.
    </h1>
    <p>
        We have received your <?= $request[ 'type' ] === 'inspection' ? 'inspection request' : 'enquiry' ?>. The UC Properties team will contact you using the
        details you provided.
    </p>
    <p class="note">
        Your reference:
        <strong><?= e( $request['reference'], ) ?></strong>
        <?php if (
    $request['type'] === 'inspection'
): ?>
        <br />
        Your preferred date is a request. Please wait for the team to confirm the appointment and
        directions before travelling.<?php endif; ?>
    </p>
    <div class="button-row">
        <a class="btn" href="<?= e( url('estates.php'), ) ?>">Continue exploring <?= icon('arrow') ?></a>
        <a class="btn btn-outline" href="tel:<?= e( phone(), ) ?>">Call the team</a>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
