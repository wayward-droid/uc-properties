<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Property is personal. So is our approach.';
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">ABOUT UC PROPERTIES</p>
            <h1>
                More than a place.
                <br />
                A possibility.
            </h1>
            <p>Real estate, investment guidance, and project management in Abuja and beyond.</p>
        </div>
    </section>
    <section class="section container wide about-grid">
        <div class="about-image">
            <img
                src="<?= e( url('assets/images/villa-valore-estate.jpg'), ) ?>"
                alt="Villa Valoré architectural rendering"
                width="800"
                height="800"
            />
            <span class="image-caption">Architectural rendering</span>
        </div>
        <div>
            <p class="eyebrow">YOUR PROPERTY JOURNEY</p>
            <h2>
                Built around people.
                <br />
                Focused on your plans.
            </h2>
            <p><?= paragraphs( setting('company_intro'), ) ?></p>
            <p>
                Whether you want to buy, sell, rent, or explore an investment, we offer guidance
                grounded in the local market and your individual goals.
            </p>
            <a class="text-link" href="<?= e( url('services.php'), ) ?>">Explore our services <?= icon( 'up-right', ) ?></a>
        </div>
    </section>
    <section class="section container wide">
        <div class="section-heading"><h2>Why work with UC?</h2></div>
        <div class="steps-grid">
            <article>
                <span>01</span>
                <h3>A local perspective</h3>
                <p>
                    Our published estate portfolio spans Idu (Gousa), Sheretti in Kabusa, and Kuje.
                </p>
            </article>
            <article>
                <span>02</span>
                <h3>A connected service</h3>
                <p>
                    Property advice, investment guidance, and project management bring different
                    parts of your plans together.
                </p>
            </article>
            <article>
                <span>03</span>
                <h3>A personal conversation</h3>
                <p>
                    Speak directly with the team about your priorities, questions, and next steps.
                </p>
            </article>
        </div>
    </section>
    <?php include __DIR__ .
    '/includes/cta.php'; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
