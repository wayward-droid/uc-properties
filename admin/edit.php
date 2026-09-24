<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require __DIR__ . '/entities.php';
require dirname(__DIR__) . '/includes/uploads.php';
$entity = input('entity');
if (!isset($entities[$entity])) {
    not_found();
}
$spec = $entities[$entity];
$id = ctype_digit(input('id')) ? (int) input('id') : 0;
$record = $id ? row('SELECT * FROM `' . $entity . '` WHERE id=?', [$id]) : [];
if ($id && !$record) {
    not_found();
}
$title = ($id ? 'Edit ' : 'Add ') . strtolower($spec['label']);
$errors = [];
$newFiles = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $values = [];
    foreach ($spec['fields'] as $key => $field) {
        $type = $field['type'];
        if (in_array($type, ['image', 'pdf', 'document'], true)) {
            $values[$key] = input('clear_' . $key) === '1' ? null : $record[$key] ?? null;
            continue;
        }
        $value = input($key);
        $choices = relation_choices($type);
        if ($type === 'checkbox') {
            $values[$key] = $value === '1' ? 1 : 0;
            continue;
        }
        if ($field['required'] && $value === '') {
            $errors[] = $field['label'] . ' is required.';
        }
        if ($choices !== null) {
            if ($value !== '' && !array_key_exists($value, $choices)) {
                $errors[] = 'Choose a valid ' . $field['label'] . '.';
            }
            $value = $value === '' ? null : (int) $value;
        } elseif ($type === 'select') {
            if (!array_key_exists($value, $field['choices'])) {
                $errors[] = 'Choose a valid ' . $field['label'] . '.';
            }
        } elseif (in_array($type, ['number', 'decimal', 'latitude', 'longitude'], true)) {
            if ($value === '') {
                $value = $key === 'sort_order' ? 0 : null;
            } else {
                if (!is_numeric($value) || !is_finite((float) $value)) {
                    $errors[] = $field['label'] . ' must be a number.';
                } elseif ($type === 'number' && (!ctype_digit($value) || (int) $value > 65535)) {
                    $errors[] = $field['label'] . ' must be a whole number from 0 to 65535.';
                } elseif (
                    $type === 'decimal' &&
                    ((float) $value < 0 ||
                        (float) $value >= 10000000000000 ||
                        !preg_match('/^\d+(\.\d{1,2})?$/', $value))
                ) {
                    $errors[] =
                        $field['label'] .
                        ' needs a non-negative amount with at most two decimal places.';
                } elseif ($type === 'latitude' && abs((float) $value) > 90) {
                    $errors[] = 'Latitude must be between -90 and 90.';
                } elseif ($type === 'longitude' && abs((float) $value) > 180) {
                    $errors[] = 'Longitude must be between -180 and 180.';
                }
            }
        } elseif ($type === 'date') {
            $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
            if ($value !== '' && (!$d || $d->format('Y-m-d') !== $value)) {
                $errors[] = 'Enter a valid date.';
            }
            $value = $value ?: null;
        } elseif ($type === 'slug' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            $errors[] = 'URL names use lowercase letters, numbers and single hyphens.';
        }
        $max = $type === 'textarea' ? 30000 : ($type === 'slug' ? 190 : 255);
        if (in_array($key, ['name', 'title'], true)) {
            $max = $entity === 'locations' ? 150 : ($entity === 'testimonials' ? 120 : 180);
        }
        if ($key === 'summary') {
            $max = 500;
        }
        if ($key === 'attribution') {
            $max = 180;
        }
        if (is_string($value) && strlen($value) > $max) {
            $errors[] = $field['label'] . ' is too long (maximum ' . $max . ' bytes).';
        }
        $values[$key] = $value;
    }
    if (
        $entity === 'estates' &&
        ($values['latitude'] === null) !== ($values['longitude'] === null)
    ) {
        $errors[] = 'Supply both map coordinates or leave both blank.';
    }
    if ($entity === 'property_options') {
        if ((float) ($values['size_sqm'] ?? 0) <= 0) {
            $errors[] = 'Plot size must be greater than zero.';
        }
        if ($values['kind'] === 'land') {
            $values['bedrooms'] = null;
        }
        if (!empty($values['price_verified'])) {
            if (
                $values['outright_price'] === null ||
                !$values['price_updated'] ||
                !$values['price_includes'] ||
                !$values['additional_charges']
            ) {
                $errors[] =
                    'Verified pricing requires outright price, date, inclusions and an additional-charge statement.';
            }
            if ($values['price_updated'] > date('Y-m-d')) {
                $errors[] = 'A price verification date cannot be in the future.';
            }
            $plan = [
                $values['instalment_total'],
                $values['deposit'],
                $values['duration_months'],
                $values['payment_schedule'],
            ];
            if (
                array_filter($plan, fn($v) => $v !== null && $v !== '') &&
                count(array_filter($plan, fn($v) => $v !== null && $v !== '')) !== 4
            ) {
                $errors[] = 'Complete all instalment fields or leave all four blank.';
            }
            if (
                $values['instalment_total'] !== null &&
                ((float) $values['deposit'] > (float) $values['instalment_total'] ||
                    (int) $values['duration_months'] < 1)
            ) {
                $errors[] = 'Check the deposit and payment duration.';
            }
        }
    }
    if ($entity === 'prototypes' && (int) ($values['bedrooms'] ?? 0) < 1) {
        $errors[] = 'A design must have at least one bedroom.';
    }
    if ($entity === 'testimonials' && $values['published'] && !$values['approved']) {
        $errors[] = 'Confirm the customer’s permission and authenticity before publishing.';
    }
    if (
        $entity === 'media' &&
        (!empty($values['estate_id']) ? 1 : 0) + (!empty($values['prototype_id']) ? 1 : 0) !== 1
    ) {
        $errors[] = 'Choose exactly one owner: an estate or a building design.';
    }
    if (!$errors) {
        try {
            foreach ($spec['fields'] as $key => $field) {
                if (!in_array($field['type'], ['image', 'pdf', 'document'], true)) {
                    continue;
                }
                $path = save_upload($key, $field['type']);
                if ($path) {
                    $values[$key] = $path;
                    $newFiles[] = $path;
                }
                if ($field['required'] && !$values[$key]) {
                    throw new RuntimeException($field['label'] . ' is required.');
                }
            }
            if (
                in_array($entity, ['estates', 'prototypes'], true) &&
                $values['published'] &&
                !$values['cover_image']
            ) {
                throw new RuntimeException('Add a main image before publishing.');
            }
            if ($entity === 'media') {
                $pdf = str_ends_with($values['path'], '.pdf');
                if (in_array($values['kind'], ['rendering', 'photograph'], true) && $pdf) {
                    throw new RuntimeException('Renderings and photographs require an image file.');
                }
                if ($values['kind'] === 'brochure' && !$pdf) {
                    throw new RuntimeException('A brochure must be a PDF.');
                }
            }
            $columns = array_keys($values);
            if ($id) {
                run(
                    'UPDATE `' .
                        $entity .
                        '` SET ' .
                        implode(',', array_map(fn($c) => '`' . $c . '`=?', $columns)) .
                        ' WHERE id=?',
                    array_merge(array_values($values), [$id]),
                );
            } else {
                run(
                    'INSERT INTO `' .
                        $entity .
                        '` (`' .
                        implode('`,`', $columns) .
                        '`) VALUES (' .
                        implode(',', array_fill(0, count($columns), '?')) .
                        ')',
                    array_values($values),
                );
            }
            flash('Your changes have been saved.');
            go('admin/content.php?entity=' . $entity);
        } catch (Throwable $ex) {
            foreach ($newFiles as $f) {
                if (is_file(ROOT . '/' . $f)) {
                    unlink(ROOT . '/' . $f);
                }
            }
            if ($ex instanceof PDOException) {
                error_log('UC content save: ' . $ex->getMessage());
                $errors[] =
                    'Could not save. Check for a duplicate URL name or association, and valid related records.';
            } else {
                $errors[] = $ex->getMessage();
            }
        }
    }
    $record = array_merge($record, $values);
    // Failed new uploads were removed; do not retain their paths on the error form.
    foreach ($newFiles as $path) {
        foreach ($record as $k => $v) {
            if ($v === $path) {
                $record[$k] = null;
            }
        };
    }
}
include __DIR__ . '/header.php';
?>
<a class="text-link mb-4" href="<?= e(url('admin/content.php?entity=' . $entity)) ?>">Back to <?= e( strtolower($spec['label']), ) ?></a>
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
<form method="post" enctype="multipart/form-data" class="form-card admin-edit-form">
    <?= csrf() ?>
    <div class="form-grid">
        <?php foreach ($spec['fields'] as $key => $field):

    $value = $record[$key] ?? '';
    $type = $field['type'];
    $choices = relation_choices($type);
    ?>
        <div class="field<?= in_array($type, ['textarea', 'checkbox', 'image', 'pdf', 'document'], true) ? ' full' : '' ?>">
            <?php if ($type === 'checkbox'): ?>
            <label class="checkbox-line">
                <input type="checkbox" name="<?= e( $key, ) ?>" value="1" <?= $value ? ' checked' : '' ?> />
                <span><?= e($field['label']) ?></span>
            </label>
            <?php else: ?>
            <label for="field-<?= e($key) ?>"><?= e($field['label']), $field['required'] ? ' *' : '' ?></label>
            <?php if ($type === 'textarea'): ?>
            <textarea id="field-<?= e($key) ?>" name="<?= e( $key, ) ?>" <?= $field['required'] ? ' required' : '' ?>>
<?= e($value) ?></textarea>
            <?php elseif ($type === 'select' || $choices !== null): ?>
            <select id="field-<?= e( $key, ) ?>" name="<?= e($key) ?>" <?= $field['required'] ? ' required' : '' ?>>
                <?php
if ($choices !== null): ?>
                <option value="">Select…</option>
                <?php endif;
foreach ($choices ?? $field['choices'] as $v => $label): ?>
                <option value="<?= e($v) ?>" <?= selected( $value, $v, ) ?>><?= e($label) ?></option>
                <?php endforeach;
?>
            </select>
            <?php elseif (in_array($type, ['image', 'pdf', 'document'], true)): ?>
            <input
                id="field-<?= e( $key, ) ?>"
                name="<?= e($key) ?>"
                type="file"
                accept="<?= $type === 'image' ? 'image/jpeg,image/png,image/webp' : ($type === 'pdf' ? 'application/pdf' : 'image/jpeg,image/png,image/webp,application/pdf') ?>"
            />
            <?php if (
    $value
): ?>
            <p class="upload-current">
                <a href="<?= e( asset($value), ) ?>" target="_blank" rel="noopener">Open current file</a>
                <label class="checkbox-line">
                    <input type="checkbox" name="clear_<?= e( $key, ) ?>" value="1" />
                    Remove current attachment
                </label>
            </p>
            <?php endif; ?>
            <small>
                JPG, PNG, WebP: 5 MB max. PDF: 10 MB max. Safe filenames are generated
                automatically.
            </small>
            <?php else: ?>
            <input
                id="field-<?= e($key) ?>"
                name="<?= e($key) ?>"
                type="<?= in_array( $type, ['number', 'decimal', 'latitude', 'longitude'], true, ) ? 'number' : ($type === 'date' ? 'date' : 'text') ?>"
                <?= in_array($type, ['decimal', 'latitude', 'longitude'], true) ? ' step="any"' : '', in_array($type, ['number', 'decimal'], true) ? ' min="0"' : '', $field['required'] ? ' required' : '' ?>
                value="<?= e($value) ?>"
            />
            <?php endif;endif; ?> <?php if ($field['help']): ?>
            <small><?= e($field['help']) ?></small>
            <?php endif; ?>
        </div>
        <?php
endforeach; ?>
        <div class="full button-row">
            <button class="btn" type="submit">Save changes <?= icon( 'check', ) ?></button>
            <a class="btn btn-outline" href="<?= e( url('admin/content.php?entity=' . $entity), ) ?>">Cancel</a>
        </div>
    </div>
</form>
<?php include __DIR__ . '/footer.php'; ?>
