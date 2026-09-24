<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Privacy information';
include __DIR__ . '/includes/header.php';
?>
<main id="main" class="container section reading">
    <p class="eyebrow">PRIVACY</p>
    <h1>Your information.</h1>
    <?php
if (
    setting('legal_approved') !== '1'
): ?>
    <p class="note">
        Draft for company review. UC Properties must approve its privacy notice, retention period,
        contact process, and service-provider disclosures before public launch.
    </p>
    <?php endif;
if (setting('privacy_text')): ?>
    <div><?= paragraphs( setting('privacy_text'), ) ?></div>
    <?php else: ?>
    <h2>When you contact us</h2>
    <p>
        The enquiry and inspection forms collect your name, phone number, optional email, selected
        property, and the details you choose to provide. Inspection requests also collect your
        preferred date and visit preference.
    </p>
    <h2>How the website uses this information</h2>
    <p>
        Requests are saved for authorised staff to review and respond to. If notifications are
        enabled, the website sends the configured staff email address a request reference and a link
        to the protected dashboard.
    </p>
    <h2>Sessions and spam prevention</h2>
    <p>
        The website uses a session cookie to protect forms and staff access. A keyed hash of the
        connection address is used to limit repeated submissions. This build does not include
        advertising cookies or third-party analytics.
    </p>
    <h2>Links to other services</h2>
    <p>
        Interactive maps request map tiles from OpenStreetMap when the map comes into view. This sends your connection address and standard browser request information to OpenStreetMap. The site does not request your device location. WhatsApp and map links open external services. Their handling of data is governed by their
        own policies. Inspection links prefill the selected property. Form buttons save your request, then open WhatsApp with your name, contact details, property selections, preferred date (if applicable), and message. You choose whether to tap Send in WhatsApp.
    </p>
    <h2>Questions about your information</h2>
    <p>
        Contact <?= e( setting('email'), ) ?> or <?= e( setting('phone_display'), ) ?> to ask about the information you submitted. The
        company needs to confirm its retention and request-handling procedures before launch.
    </p>
    <?php endif;
?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
