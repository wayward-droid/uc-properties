<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Let’s talk about your plans';
$formType = 'enquiry';
require __DIR__ . '/includes/request-handler.php';
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">CONTACT UC PROPERTIES</p>
            <h1>
                A good conversation
                <br />
                is a great beginning.
            </h1>
            <p>Ask a question, tell us about your plans, or get help choosing an estate.</p>
        </div>
    </section>
    <section class="container wide section form-layout">
        <div>
            <h2>Let’s find your next step.</h2>
            <div class="contact-method">
                <h3><?= icon( 'phone', ) ?> Call or WhatsApp</h3>
                <a href="tel:<?= e(phone()) ?>"><?= e( setting('phone_display'), ) ?></a>
                <p>
                    <a class="text-link" href="<?= e( whatsapp(), ) ?>" target="_blank" rel="noopener">
                        Start a WhatsApp conversation <?= icon( 'up-right', ) ?>
                    </a>
                </p>
            </div>
            <div class="contact-method">
                <h3><?= icon( 'mail', ) ?> Email our team</h3>
                <a href="mailto:<?= e(setting('email')) ?>"><?= e( setting('email'), ) ?></a>
            </div>
            <div class="contact-method">
                <h3><?= icon('pin') ?> Visit the office</h3>
                <p><?= e( setting('address'), ) ?></p>
                <p>Please call ahead to arrange your visit.</p>
            </div>
        </div>
        <div><?php include __DIR__ .
    '/includes/request-form.php'; ?></div>
    </section>
</main>
<?php include __DIR__ .
    '/includes/footer.php'; ?>
