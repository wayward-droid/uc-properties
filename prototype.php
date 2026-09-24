<?php
require __DIR__ . '/includes/bootstrap.php';
$prototype = row('SELECT * FROM prototypes WHERE slug=? AND published=1', [input('slug')]);
if (!$prototype) {
    not_found();
}
$title = $prototype['name'];
$compatible = rows(
    'SELECT o.*,e.name AS estate_name,e.slug AS estate_slug FROM compatibility c JOIN property_options o ON o.id=c.option_id JOIN estates e ON e.id=o.estate_id WHERE c.prototype_id=? AND c.approved=1 AND o.published=1 AND e.published=1 ORDER BY e.name,o.size_sqm',
    [$prototype['id']],
);
$selection = null;
foreach ($compatible as $o) {
    if ((int) $o['id'] === (int) input('option')) {
        $selection = $o;
    }
}
$media = rows(
    "SELECT * FROM media WHERE prototype_id=? AND kind IN ('rendering','photograph') ORDER BY sort_order,id",
    [$prototype['id']],
);
$documents = rows(
    "SELECT * FROM media WHERE prototype_id=? AND kind IN ('floor_plan','brochure') ORDER BY sort_order,id",
    [$prototype['id']],
);
$whatsapp_message =
    'Hello UC Properties, I would like to discuss the ' .
    $prototype['name'] .
    ' design. ' .
    url('prototype.php?slug=' . $prototype['slug']);
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="container wide section">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= e( url('prototypes.php'), ) ?>">Building designs</a>
            <span>/</span>
            <span><?= e( $prototype['name'], ) ?></span>
        </nav>
        <p class="eyebrow">PROPOSED BUILDING DESIGN</p>
        <h1><?= e( $prototype['name'], ) ?></h1>
        <p><?= (int) $prototype['bedrooms'] ?> bedrooms<?= $prototype['bathrooms'] ? ' · ' . (int) $prototype['bathrooms'] . ' bathrooms' : '' ?></p>
        <div class="detail-hero">
            <img
                id="gallery-main"
                src="<?= e( picture($prototype), ) ?>"
                alt="<?= e( $prototype['name'], ) ?> architectural rendering"
                width="1280"
                height="700"
            />
            <span id="gallery-caption" class="image-caption">
                Architectural rendering · Proposed design
            </span>
        </div>
        <?php if ($media): ?>
        <div class="gallery-strip">
            <?php foreach (
    $media
    as $m
): ?>
            <button
                type="button"
                data-gallery-src="<?= e(asset($m['path'])) ?>"
                data-gallery-alt="<?= e( $m['alt'], ) ?>"
                data-gallery-kind="<?= e(tag($m['kind'])) ?>"
                aria-pressed="false"
                aria-label="Show <?= e( $m['alt'], ) ?>"
            >
                <img src="<?= e(asset($m['path'])) ?>" alt="<?= e( $m['alt'], ) ?>" />
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="detail-layout">
            <div>
                <section class="detail-section">
                    <h2>Imagine life here.</h2>
                    <p><?= paragraphs( $prototype['description'], ) ?></p>
                    <p><?= paragraphs( $prototype['specifications'], ) ?></p>
                </section>
                <section class="detail-section">
                    <h2>What the offer includes.</h2>
                    <p><?= paragraphs( $prototype['offer_includes'], ) ?></p>
                    <?php if ($prototype['floor_plan']): ?>
                    <a class="text-link" href="<?= e( asset($prototype['floor_plan']), ) ?>" target="_blank" rel="noopener">
                        View the supplied floor plan <?= icon( 'up-right', ) ?>
                    </a>
                    <?php else: ?>
                    <p class="note">
                        Ask the team for a confirmed floor plan and full construction
                        specifications.
                    </p>
                    <?php endif; ?>
                </section>
                <?php foreach (
    $documents
    as $document
): ?>
                <p>
                    <a class="text-link" href="<?= e( asset($document['path']), ) ?>" target="_blank" rel="noopener">
                        <?= e($document['alt']) ?> <?= icon( 'arrow', ) ?>
                    </a>
                </p>
                <?php endforeach; ?>
                <section class="detail-section">
                    <h2>Find a suitable plot.</h2>
                    <?php
if (
    !$compatible
): ?>
                    <p>
                        The team is reviewing specific estate and plot associations for this design.
                        Contact us to discuss suitability.
                    </p>
                    <?php endif;
foreach ($compatible as $o): ?>
                    <div class="option-card">
                        <h3><?= e($o['estate_name']) ?> · <?= e( $o['name'], ) ?></h3>
                        <a class="btn btn-small" href="<?= e( inspection_whatsapp((int) $o['estate_id'], (int) $o['id'], (int) $prototype['id']), ) ?>">
                            Request an inspection <?= icon('arrow') ?>
                        </a>
                    </div>
                    <?php endforeach;
?>
                </section>
            </div>
            <aside class="side-card">
                <p class="eyebrow">MAKE IT PERSONAL</p>
                <h2>Start with a conversation.</h2>
                <p>Tell us about your plans and ask what works for your chosen plot.</p>
                <?php if (
    $selection
): ?>
                <p class="note"><?= e( $selection['estate_name'] . ' · ' . $selection['name'], ) ?></p>
                <a class="btn" href="<?= e( inspection_whatsapp((int) $selection['estate_id'], (int) $selection['id'], (int) $prototype['id']), ) ?>">Inspect this selection</a>
                <?php endif; ?>
                <a class="btn btn-outline" href="<?= e( whatsapp($whatsapp_message), ) ?>" target="_blank" rel="noopener">
                    <?= icon( 'whatsapp', ) ?> Discuss this design
                </a>
                <a class="btn btn-outline" href="tel:<?= e(phone()) ?>">
                    <?= icon( 'phone', ) ?> Call the team
                </a>
            </aside>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
