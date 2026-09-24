<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
$title = 'Company settings';
$errors = [];
$fields = [
    'phone' => 'Telephone destination (+country code)',
    'phone_display' => 'Telephone display format',
    'whatsapp' => 'WhatsApp number (digits only)',
    'email' => 'Company email',
    'address' => 'Office address',
    'company_intro' => 'Company introduction',
    'privacy_text' => 'Approved privacy notice text',
    'terms_text' => 'Approved website terms text',
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $new = [];
    foreach ($fields as $key => $label) {
        $new[$key] = input($key);
        if (strlen($new[$key]) > 30000) {
            $errors[] = $label . ' is too long.';
        }
    }
    if (!preg_match('/^\+[1-9]\d{6,14}$/', $new['phone'])) {
        $errors[] = 'Use a phone number such as +2348065741674.';
    }
    if (
        preg_replace('/\D/', '', $new['phone_display']) !== preg_replace('/\D/', '', $new['phone'])
    ) {
        $errors[] = 'The displayed phone number must match the telephone destination.';
    }
    if (!preg_match('/^[1-9]\d{6,14}$/', $new['whatsapp'])) {
        $errors[] = 'WhatsApp needs country code and digits only.';
    }
    if (!filter_var($new['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!$new['address'] || !$new['company_intro']) {
        $errors[] = 'Add the office address and company introduction.';
    }
    $new['legal_approved'] = input('legal_approved') === '1' ? '1' : '0';
    if ($new['legal_approved'] === '1' && (!$new['privacy_text'] || !$new['terms_text'])) {
        $errors[] = 'Supply approved privacy and terms text before marking them approved.';
    }
    if (!$errors) {
        $db->beginTransaction();
        try {
            foreach ($new as $key => $value) {
                run(
                    'INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)',
                    [$key, $value],
                );
            }
            $db->commit();
            flash('Company settings updated across the website.');
            go('admin/settings.php');
        } catch (Throwable $e) {
            $db->rollBack();
            $errors[] = 'Settings could not be saved. Please try again.';
        }
    }
    $settings = array_merge($settings, $new);
}
include __DIR__ . '/header.php';
?>
<p class="note">
    Telephone, WhatsApp and company contact information are shared across the site. Notification
    delivery settings are controlled separately in the private configuration file.
</p>
<?php if ($errors): ?>
<div class="notice error" role="alert">
    <ul>
        <?php foreach (
    $errors
    as $error
): ?>
        <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<form method="post" class="form-card">
    <?= csrf() ?>
    <div class="form-grid">
        <?php foreach (
    $fields
    as $key => $label
): ?>
        <div class="field<?= in_array( $key, ['address', 'company_intro', 'privacy_text', 'terms_text'], true, ) ? ' full' : '' ?>">
            <label for="<?= e($key) ?>"><?= e($label) ?></label>
            <?php if (
    in_array($key, ['address', 'company_intro', 'privacy_text', 'terms_text'], true)
): ?>
            <textarea id="<?= e($key) ?>" name="<?= e($key) ?>"><?= e( setting($key), ) ?></textarea>
            <?php else: ?>
            <input
                id="<?= e($key) ?>"
                name="<?= e($key) ?>"
                type="<?= $key === 'email' ? 'email' : 'text' ?>"
                value="<?= e( setting($key), ) ?>"
                required
            />
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <label class="checkbox-line full">
            <input type="checkbox" name="legal_approved" value="1" <?= setting( 'legal_approved', ) === '1' ? ' checked' : '' ?> />
            <span>The company has approved the privacy and terms text above for public use.</span>
        </label>
        <div class="full">
            <button class="btn" type="submit">Save settings <?= icon( 'check', ) ?></button>
        </div>
    </div>
</form>
<?php include __DIR__ . '/footer.php'; ?>
