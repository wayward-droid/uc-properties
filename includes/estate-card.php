<?php
// Card specifications and prices use the same published options as the details page.
$cardOptions = rows(
    'SELECT size_sqm, kind, bedrooms, outright_price, price_verified FROM property_options WHERE estate_id=? AND published=1 ORDER BY size_sqm',
    [$estate['id']]
);
$cardPrice = null;
foreach ($cardOptions as $cardOption) {
    if ($cardOption['price_verified'] && $cardOption['outright_price'] !== null &&
        ($cardPrice === null || (float)$cardOption['outright_price'] < (float)$cardPrice['outright_price'])) {
        $cardPrice = $cardOption;
    }
}
?>
<article class="estate-card reveal">
    <a class="card-image" href="<?= e( url('estate.php?slug=' . $estate['slug']), ) ?>">
        <img
            src="<?= e(picture($estate)) ?>"
            alt="<?= e($estate['name']) ?> — <?= e( $estate['image_kind'], ) ?>"
            width="640"
            height="430"
            loading="lazy"
        />
        <span class="pill"><?= $estate['category'] === 'land' ? 'LAND & PLOT OPTIONS' : e(tag($estate['category'])) ?></span>
        <span class="image-caption"><?= $estate['image_kind'] === 'rendering' ? 'Architectural rendering' : 'Property photograph' ?></span>
    </a>
    <div class="card-body">
        <p class="location"><?= icon('pin') ?> <?= e( $estate['location_name'], ) ?></p>
        <h3><a href="<?= e(url('estate.php?slug=' . $estate['slug'])) ?>"><?= e( $estate['name'], ) ?></a></h3>
        <p class="card-summary"><?= e($estate['summary']) ?></p>
        <?php if ($cardOptions): ?>
            <p class="card-specs">
                <?= icon('land') ?>
                <?= number_format((float)$cardOptions[0]['size_sqm']) ?><?= count($cardOptions)>1 ? '–'.number_format((float)$cardOptions[count($cardOptions)-1]['size_sqm']) : '' ?> m² options
            </p>
        <?php endif; ?>
        <p class="card-price">
            <?php if ($cardPrice): ?>
                From <?= money($cardPrice['outright_price']) ?>
                <small>Confirmed outright <?= $cardPrice['kind']==='land' ? 'land' : 'home' ?> price</small>
            <?php else: ?>
                Contact us for current pricing
            <?php endif; ?>
        </p>
        <div class="card-foot">
            <span><?= $estate[ 'availability' ] === 'enquire' ? 'Enquire for availability' : e(tag($estate['availability'])) ?></span>
            <a class="round-link" href="<?= e( url('estate.php?slug=' . $estate['slug']), ) ?>" aria-label="Explore <?= e($estate['name']) ?>">
                <?= icon('up-right') ?>
            </a>
        </div>
    </div>
</article>
