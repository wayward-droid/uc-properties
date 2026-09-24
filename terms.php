<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Website terms';
include __DIR__ . '/includes/header.php';
?>
<main id="main" class="container section reading">
    <p class="eyebrow">WEBSITE TERMS</p>
    <h1>Before your next step.</h1>
    <?php
if (
    setting('legal_approved') !== '1'
): ?>
    <p class="note">
        Draft for company review. This website text must be reviewed and approved by UC Properties
        and its legal adviser before public launch.
    </p>
    <?php endif;
if (setting('terms_text')): ?>
    <div><?= paragraphs( setting('terms_text'), ) ?></div>
    <?php else: ?>
    <h2>Property information</h2>
    <p>
        Website details are provided to help you explore options and ask questions. Confirm
        availability, location, dimensions, documentation, and all commercial terms with the team
        for the specific property you are considering.
    </p>
    <h2>Proposed designs and illustrations</h2>
    <p>
        Architectural renderings illustrate designs. They do not establish that a building is
        completed or that every illustrated feature is included in a price. Ask for approved
        specifications and a written statement of what is included.
    </p>
    <h2>Prices and payments</h2>
    <p>
        Request a current written quotation setting out the outright or instalment total, deposit,
        schedule, additional charges, and exactly what is being purchased. The site does not collect
        payments or establish a purchase agreement.
    </p>
    <h2>Inspection requests</h2>
    <p>
        Submitting a preferred date is a request. Wait for the company to confirm the appointment
        and arrangements.
    </p>
    <h2>Transaction-specific terms</h2>
    <p>
        Ownership, allocation, payment, cancellation, and refund terms must be supplied and agreed
        through the company’s approved transaction documents. This draft does not define those
        policies.
    </p>
    <?php endif;
?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
