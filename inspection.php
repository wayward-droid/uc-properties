<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Book an inspection';
$formType = 'inspection';
require __DIR__ . '/includes/request-handler.php';
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">SEE THE POSSIBILITIES</p>
            <h1>Let’s show you around.</h1>
            <p>Choose an estate and a preferred date, then continue to WhatsApp. No account needed.</p>
        </div>
    </section>
    <section class="container wide section form-layout">
        <div>
            <h2>Your visit starts here.</h2>
            <p>
                Explore the location, ask your questions, and get a clearer picture of your options.
            </p>
            <ol>
                <li>Fill in your preferred date and contact details.</li>
                <li>Tap Request an inspection, then send the prepared message on WhatsApp.</li>
                <li>Receive confirmation and directions before travelling.</li>
            </ol>
            <p class="note">
                This is an inspection request. Your appointment is confirmed only when the team
                contacts you.
            </p>
            <a class="text-link" href="tel:<?= e( phone(), ) ?>">Prefer to call? <?= e(setting('phone_display')) ?></a>
        </div>
        <div><?php include __DIR__ .
    '/includes/request-form.php'; ?></div>
    </section>
</main>
<?php include __DIR__ .
    '/includes/footer.php'; ?>
