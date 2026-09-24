<footer class="site-footer">
    <div class="container wide footer-grid">
        <div class="footer-brand">
<a href="<?= e(url()) ?>">
<img class="logo logo-white" src="<?= e( url('assets/images/uc-logo.png'), ) ?>" alt="UC Properties Limited" width="400" height="110" loading="lazy">
</a>
<p>Spaces for your plans.<br>A place for your future.</p>
<a class="text-link light" href="<?= e( url('inspection.php'), ) ?>">Plan your visit <?= icon('up-right') ?></a>
</div>
        <div>
<h2>Explore</h2>
<a href="<?= e(url('estates.php')) ?>">Our estates</a>
<a href="<?= e( url('prototypes.php'), ) ?>">Building designs</a>
<a href="<?= e(url('services.php')) ?>">Our services</a>
<a href="<?= e( url('updates.php'), ) ?>">Project updates</a>
</div>
        <div>
<h2>Get to know us</h2>
<a href="<?= e( url('about.php'), ) ?>">About UC Properties</a>
<a href="<?= e( url('faq.php'), ) ?>">Your questions, answered</a>
<a href="<?= e( url('contact.php'), ) ?>">Contact us</a>
<a href="<?= e(url('admin/')) ?>">Staff sign in</a>
</div>
        <div>
<h2>We’re here to help</h2>
<a href="tel:<?= e(phone()) ?>"><?= e( setting('phone_display'), ) ?></a>
<a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a>
<p><?= e( setting('address'), ) ?></p>
<a href="<?= e(whatsapp()) ?>" target="_blank" rel="noopener">Chat on WhatsApp <?= icon( 'up-right', ) ?></a>
</div>
    </div>
    <div class="container wide footer-bottom">
<span>© <?= date( 'Y', ) ?> UC Properties Limited. All rights reserved.</span>
<div>
<a href="<?= e( url('privacy.php'), ) ?>">Privacy</a>
<a href="<?= e(url('terms.php')) ?>">Terms</a>
</div>
</div>
</footer>
<a class="whatsapp-float" href="<?= e( whatsapp( $whatsapp_message ?? 'Hello UC Properties, I would like to find out more about your estates.', ), ) ?>" target="_blank" rel="noopener" aria-label="Chat with UC Properties on WhatsApp"><?= icon( 'whatsapp', ) ?></a>
<div class="mobile-contact">
<a href="tel:<?= e(phone()) ?>"><?= icon( 'phone', ) ?> Call us</a>
<a href="<?= e( inspection_whatsapp((int) ($currentEstateId ?? 0)), ) ?>"><?= icon('calendar') ?> Book an inspection</a>
</div>
</body>
</html>
