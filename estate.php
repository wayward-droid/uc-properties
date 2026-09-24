<?php
require __DIR__ . '/includes/bootstrap.php';
$estate = row(
    'SELECT e.*,l.name AS location_name FROM estates e JOIN locations l ON l.id=e.location_id WHERE e.slug=? AND e.published=1',
    [input('slug')],
);
if (!$estate) {
    not_found();
}
$currentEstateId = (int) $estate['id'];
$title = $estate['name'];
$description = $estate['summary'];
$options = rows(
    'SELECT * FROM property_options WHERE estate_id=? AND published=1 ORDER BY size_sqm,id',
    [$estate['id']],
);
$gallery = rows(
    "SELECT * FROM media WHERE estate_id=? AND kind IN ('rendering','photograph') ORDER BY sort_order,id",
    [$estate['id']],
);
$documents = rows(
    "SELECT * FROM media WHERE estate_id=? AND kind IN ('floor_plan','brochure') ORDER BY sort_order,id",
    [$estate['id']],
);
$related = rows(
    'SELECT e.*,l.name AS location_name FROM estates e JOIN locations l ON e.location_id=l.id WHERE e.published=1 AND e.id<>? ORDER BY e.featured DESC LIMIT 3',
    [$estate['id']],
);
$whatsapp_message =
    'Hello UC Properties, I am interested in ' .
    $estate['name'] .
    '. ' .
    url('estate.php?slug=' . $estate['slug']);
$hasMap = true;
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="container wide section">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= e( url('estates.php'), ) ?>">Our estates</a>
            <span>/</span>
            <span><?= e( $estate['name'], ) ?></span>
        </nav>
        <div class="detail-title">
            <div>
                <p class="eyebrow"><?= e( $estate['location_name'], ) ?> · ABUJA</p>
                <h1><?= e($estate['name']) ?></h1>
                <p><?= e( $estate['summary'], ) ?></p>
            </div>
            <a class="text-link" href="#plot-options">Explore plot options <?= icon( 'arrow', ) ?></a>
        </div>
        <div class="detail-hero">
            <img
                id="gallery-main"
                src="<?= e(picture($estate)) ?>"
                alt="<?= e( $estate['name'] . ' — ' . $estate['image_kind'], ) ?>"
                width="1280"
                height="650"
            />
            <span class="image-caption" id="gallery-caption"><?= $estate[ 'image_kind' ] === 'rendering' ? 'Architectural rendering' : 'Property photograph' ?></span>
        </div>
        <?php if (
    $gallery
): ?>
        <div class="gallery-strip" aria-label="Estate image gallery">
            <button
                type="button"
                data-gallery-src="<?= e( picture($estate), ) ?>"
                data-gallery-alt="<?= e($estate['name']) ?>"
                data-gallery-kind="<?= e( tag($estate['image_kind']), ) ?>"
                aria-pressed="true"
                aria-label="Show main image"
            >
                <img src="<?= e( picture($estate), ) ?>" alt="Main estate view" />
            </button>
            <?php foreach (
    $gallery
    as $image
): ?>
            <button
                type="button"
                data-gallery-src="<?= e( asset($image['path']), ) ?>"
                data-gallery-alt="<?= e($image['alt']) ?>"
                data-gallery-kind="<?= e( tag($image['kind']), ) ?>"
                aria-pressed="false"
                aria-label="Show <?= e($image['alt']) ?>"
            >
                <img src="<?= e( asset($image['path']), ) ?>" alt="<?= e( $image['alt'], ) ?>" loading="lazy" />
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="detail-layout">
            <div>
                <section class="detail-section">
                    <p class="eyebrow">THE BIGGER PICTURE</p>
                    <h2>Room to imagine.</h2>
                    <p><?= paragraphs( $estate['description'], ) ?></p>
                    <dl>
                        <dt>Offer type</dt>
                        <dd><?= $estate['category'] === 'land' ? 'Land options with proposed building designs' : e(tag($estate['category'])) ?></dd>
                        <dt>Availability</dt>
                        <dd><?= $estate['availability'] === 'enquire' ? 'Contact the team to confirm available plots.' : e(tag($estate['availability'])) ?></dd>
                        <dt>Development</dt>
                        <dd><?= e( $estate['development_status'], ) ?></dd>
                        <dt>Location</dt>
                        <dd><?= e($estate['address']) ?></dd>
                    </dl>
                    <?php if (
    $estate['latitude'] !== null &&
    $estate['longitude'] !== null
): ?>
                    <a class="text-link" href="<?= e( 'https://www.google.com/maps/search/?api=1&query=' . $estate['latitude'] . ',' . $estate['longitude'], ) ?>" target="_blank" rel="noopener">
                        View supplied map location <?= icon( 'up-right', ) ?>
                    </a>
                    <?php else: ?>
                    <p class="note">
                        Please contact the team for precise directions before visiting.
                    </p>
                    <?php endif; ?>
                </section>
                <?php if (
    $estate['amenities']
): ?>
                <section class="detail-section">
                    <h2>Planned estate features.</h2>
                    <ul class="amenities">
                        <?php foreach (
    preg_split('/\r?\n/', $estate['amenities'])
    as $a
):
    if (!trim($a)) {
        continue;
    } ?>
                        <li><?= icon('check'), e($a) ?></li>
                        <?php
endforeach; ?>
                    </ul>
                    <p class="note">
                        These features are published for the estate. Ask which are installed,
                        operational, or planned for your selected phase.
                    </p>
                </section>
                <?php endif; ?>
                <section class="detail-section" id="plot-options">
                    <p class="eyebrow">CHOOSE YOUR STARTING POINT</p>
                    <h2>Plot & property options.</h2>
                    <?php if (
    !$options
): ?>
                    <p>Contact the team for currently available plot options.</p>
                    <?php endif; ?> <?php foreach ($options as $option): ?>
                    <article class="option-card">
                        <h3><?= e( $option['name'], ) ?></h3>
                        <p class="option-meta">
                            <?= number_format((float) $option['size_sqm']) ?> m² · <?= $option['kind'] === 'home' ? 'Completed home' : 'Land option', $option['kind'] === 'home' && $option['bedrooms'] ? ' · ' . (int) $option['bedrooms'] . ' bedrooms' : '' ?> · <?= $option['availability'] === 'enquire' ? 'Availability on request' : e(tag($option['availability'])) ?>
                        </p>
                        <p class="price"><?= $option['price_verified'] && $option['outright_price'] !== null ? money($option['outright_price']) . ' outright' : 'Contact us for current pricing' ?></p>
                        <p><?= e( $option['price_includes'] ?: 'Ask for a written breakdown of land, construction, and any additional charges.', ) ?></p>
                        <?php if (
    $option['price_verified']
): ?>
                        <details class="pricing-details">
                            <summary>Payment information</summary>
                            <dl>
                                <dt>Outright price</dt>
                                <dd><?= money( $option['outright_price'], ) ?></dd>
                                <dt>Instalment total</dt>
                                <dd><?= money( $option['instalment_total'], ) ?></dd>
                                <dt>Initial deposit</dt>
                                <dd><?= money( $option['deposit'], ) ?></dd>
                                <dt>Duration</dt>
                                <dd><?= $option['duration_months'] ? (int) $option['duration_months'] . ' months' : 'Confirm with the team' ?></dd>
                                <dt>Payment schedule</dt>
                                <dd><?= paragraphs( $option['payment_schedule'] ?: 'Confirm with the team', ) ?></dd>
                                <dt>Additional charges</dt>
                                <dd><?= paragraphs( $option['additional_charges'] ?: 'Confirm with the team', ) ?></dd>
                                <dt>Last price update</dt>
                                <dd><?= e( $option['price_updated'] ?: 'Confirm with the team', ) ?></dd>
                            </dl>
                        </details>
                        <?php endif; ?>
                        <div class="button-row">
                            <a class="btn btn-small" href="<?= e( url('prototypes.php?estate=' . (int) $estate['id'] . '&option=' . (int) $option['id']), ) ?>">
                                Explore suitable designs <?= icon( 'arrow', ) ?>
                            </a>
                            <a class="btn btn-small btn-outline" href="<?= e( inspection_whatsapp((int) $estate['id'], (int) $option['id']), ) ?>">
                                Request inspection
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </section>
                <section class="detail-section">
                    <h2>Know what you’re buying.</h2>
                    <p><?= paragraphs( $estate['documentation'], ) ?></p>
                    <?php
if ($estate['brochure']): ?>
                    <a class="text-link" href="<?= e( asset($estate['brochure']), ) ?>" download>
                        Download estate brochure <?= icon('arrow') ?>
                    </a>
                    <?php endif;
foreach ($documents as $document): ?>
                    <p>
                        <a class="text-link" href="<?= e( asset($document['path']), ) ?>" download>
                            <?= e($document['alt']) ?> <?= icon('arrow') ?>
                        </a>
                    </p>
                    <?php endforeach;
?>
                </section>
            </div>
            <aside class="side-card">
                <p class="eyebrow">LET’S EXPLORE TOGETHER</p>
                <h2>
                    See the possibilities
                    <br />
                    for yourself.
                </h2>
                <p>
                    Tell us when you would like to visit. Our team will contact you to confirm
                    arrangements.
                </p>
                <a class="btn" href="<?= e( inspection_whatsapp((int) $estate['id']), ) ?>">Book an inspection <?= icon('calendar') ?></a>
                <a class="btn btn-outline" href="<?= e( whatsapp($whatsapp_message), ) ?>" target="_blank" rel="noopener">
                    <?= icon( 'whatsapp', ) ?> Ask on WhatsApp
                </a>
                <a class="btn btn-outline" href="tel:<?= e(phone()) ?>">
                    <?= icon( 'phone', ) ?> <?= e(setting('phone_display')) ?>
                </a>
            </aside>
        </div>
    </section>
    <section class="section container wide inner-map">
        <div class="section-heading"><div><p class="eyebrow">EXPLORE THE LOCATION</p><h2>Get to know the area.</h2></div><p>Plan your visit with our team.</p></div>
        <?php $mapEstates = [$estate]; include __DIR__ . '/includes/estate-map.php'; ?>
    </section>
    <?php if (
    $related
): ?>
    <section class="section container wide">
        <div class="section-heading"><h2>Other places. New possibilities.</h2></div>
        <div class="card-grid"><?php foreach (
    $related
    as $estate
) {
    include __DIR__ . '/includes/estate-card.php';
} ?></div>
    </section>
    <?php endif; ?>
</main>
<?php
unset($estate);
include __DIR__ . '/includes/footer.php';
 ?>
