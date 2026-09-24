<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Your questions, answered';
$faqs = rows('SELECT * FROM faqs WHERE published=1 ORDER BY sort_order,id');
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">FREQUENTLY ASKED QUESTIONS</p>
            <h1>
                A little clarity
                <br />
                goes a long way.
            </h1>
            <p>Practical answers to help you explore our estates and plan your next step.</p>
        </div>
    </section>
    <section class="section container reading">
        <?php if (
    $faqs
):
    include __DIR__ . '/includes/faqs.php';
else:
     ?>
        <p>
            Have a question?
            <a href="<?= e(url('contact.php')) ?>">Talk to our team.</a>
        </p>
        <?php
endif; ?>
    </section>
    <?php include __DIR__ . '/includes/cta.php'; ?>
</main>
<?php include __DIR__ .
    '/includes/footer.php'; ?>
