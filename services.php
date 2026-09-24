<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Our services';
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">OUR SERVICES</p>
            <h1>
                Support for every
                <br />
                part of your plans.
            </h1>
            <p>
                Bring your property questions to a team working across real estate, investment, and
                project management.
            </p>
        </div>
    </section>
    <div class="section container reading">
        <section class="detail-section" id="real-estate">
            <p class="eyebrow">01 · REAL ESTATE</p>
            <h2>A clearer view of property.</h2>
            <p>
                Our real estate services include property valuation, market analysis and advisory,
                and property management. We help individuals and businesses understand their options
                in Abuja and beyond.
            </p>
            <a class="text-link" href="<?= e( url('contact.php'), ) ?>">
                Discuss your property needs <?= icon( 'up-right', ) ?>
            </a>
        </section>
        <section class="detail-section" id="investment">
            <p class="eyebrow">02 · INVESTMENT</p>
            <h2>Explore your opportunities.</h2>
            <p>
                We help clients explore opportunities within real estate and consider how a property
                fits their goals. Speak with the team about the specific offer, costs,
                documentation, and factors relevant to your decision.
            </p>
            <a class="text-link" href="<?= e( url('contact.php'), ) ?>">
                Start an investment conversation <?= icon( 'up-right', ) ?>
            </a>
        </section>
        <section class="detail-section" id="project-management">
            <p class="eyebrow">03 · PROJECT MANAGEMENT</p>
            <h2>From concept to completion.</h2>
            <p>
                Our project management service supports real estate developments through planning
                and delivery. Contact us to discuss the scope, responsibilities, programme, and
                requirements of your project.
            </p>
            <a class="text-link" href="<?= e( url('contact.php'), ) ?>">
                Tell us about your project <?= icon('up-right') ?>
            </a>
        </section>
    </div>
    <?php include __DIR__ .
    '/includes/cta.php'; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
