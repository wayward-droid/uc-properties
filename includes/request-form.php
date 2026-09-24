<?php if (
    $errors
): ?>
<div class="notice error" role="alert" tabindex="-1">
    <strong>Please check the following:</strong>
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
<form method="post" action="<?= e( url($formType === 'inspection' ? 'inspection.php' : 'contact.php'), ) ?>" class="form-card" data-submit-once>
    <?= csrf() ?>
    <div class="honeypot" aria-hidden="true">
        <label for="website">Leave this empty</label>
        <input id="website" name="website" tabindex="-1" autocomplete="off" />
    </div>
    <div class="form-grid">
        <div class="field full">
            <label for="request-estate">Estate<?= $formType === 'inspection' ? ' <span class="required">*</span>' : ' (optional)' ?></label>
            <select id="request-estate" name="estate" <?= $formType === 'inspection' ? ' required' : '' ?>>
                <option value="">Choose an estate</option>
                <?php foreach (
    $estates
    as $e
): ?>
                <option value="<?= (int) $e['id'] ?>" <?= selected($chosenEstate, $e['id']) ?>><?= e( $e['name'], ) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($formType === 'inspection'): ?>
        <div class="field">
            <label for="request-option">Plot option (optional)</label>
            <select id="request-option" name="option">
                <option value="">Help me choose</option>
                <?php foreach (
    $formOptions
    as $o
): ?>
                <option value="<?= (int) $o['id'] ?>" data-estate="<?= (int) $o['estate_id'] ?>" <?= selected( $chosenOption, $o['id'], ) ?>>
                    <?= e($o['estate_name'] . ' · ' . $o['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="request-prototype">Building design (optional)</label>
            <select id="request-prototype" name="prototype">
                <option value="">Discuss with the team</option>
                <?php foreach (
    $formPrototypes
    as $p
): ?>
                <option value="<?= (int) $p['id'] ?>" data-option="<?= (int) $p['option_id'] ?>" <?= selected( $chosenPrototype, $p['id'], ) ?>>
                    <?= e( $p['name'], ) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small>Only designs approved for your plot are shown.</small>
        </div>
        <?php endif; ?>
        <div class="field">
            <label for="name">
                Your name
                <span class="required">*</span>
            </label>
            <input
                id="name"
                name="name"
                autocomplete="name"
                required
                minlength="2"
                maxlength="120"
                value="<?= e( input('name'), ) ?>"
            />
        </div>
        <div class="field">
            <label for="phone">
                Phone number
                <span class="required">*</span>
            </label>
            <input
                id="phone"
                name="phone"
                type="tel"
                autocomplete="tel"
                required
                maxlength="30"
                placeholder="+234…"
                value="<?= e( input('phone'), ) ?>"
            />
        </div>
        <div class="field full">
            <label for="email">Email address (optional)</label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                maxlength="190"
                value="<?= e( input('email'), ) ?>"
            />
        </div>
        <?php if ($formType === 'inspection'): ?>
        <div class="field">
            <label for="preferred-date">
                Preferred date
                <span class="required">*</span>
            </label>
            <input
                id="preferred-date"
                name="preferred_date"
                type="date"
                min="<?= date( 'Y-m-d', ) ?>"
                max="<?= date('Y-m-d', strtotime('+1 year')) ?>"
                required
                value="<?= e( input('preferred_date'), ) ?>"
            />
        </div>
        <div class="field">
            <label for="visit">
                Visit preference
                <span class="required">*</span>
            </label>
            <select id="visit" name="visit_preference" required>
                <option value="">Choose a preference</option>
                <?php foreach (
    [
        'in_person' => 'In-person inspection',
        'virtual' => 'Ask about a virtual visit',
        'call' => 'Call me to discuss',
    ]
    as $v => $label
): ?>
                <option value="<?= e($v) ?>" <?= selected(input('visit_preference'), $v) ?>><?= e( $label, ) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="field full">
            <label for="message"><?= $formType === 'inspection' ? 'Anything else? (optional)' : 'How can we help? <span class="required">*</span>' ?></label>
            <textarea
                id="message"
                name="message"
                maxlength="5000"
                <?= $formType === 'enquiry' ? ' required minlength="5"' : '' ?>
                placeholder="Tell us a little about your plans…"
            >
<?= e( input('message'), ) ?></textarea>
        </div>
        <label class="checkbox-line full">
            <input type="checkbox" name="consent" value="1" required<?= input( 'consent', ) === '1' ? ' checked' : '' ?> />
            <span>
                I agree that UC Properties may save and contact me about this request, and prepare these details for WhatsApp. Read the
                <a href="<?= e( url('privacy.php'), ) ?>">privacy information</a>
                .
            </span>
        </label>
        <div class="full">
            <button type="submit" class="btn"><?= $formType === 'inspection' ? 'Request an inspection' : 'Send your enquiry' ?> <?= icon('whatsapp') ?></button>
            <p class="form-help mt-3 mb-0"><small>Opens WhatsApp with your details. Tap Send there to start the conversation.<?= $formType === 'inspection' ? ' Our team will confirm your appointment.' : '' ?></small></p>
        </div>
    </div>
</form>
