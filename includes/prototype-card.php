<article class="prototype-card reveal">
    <a class="card-image" href="<?= e( url( 'prototype.php?slug=' . $prototype['slug'] . (!empty($selected_option) ? '&option=' . (int) $selected_option : ''), ), ) ?>">
        <img
            src="<?= e(picture($prototype)) ?>"
            alt="Architectural rendering of <?= e( $prototype['name'], ) ?>"
            width="600"
            height="400"
            loading="lazy"
        />
        <span class="image-caption">Proposed design · Rendering</span>
    </a>
    <div class="card-body">
        <span class="eyebrow"><?= (int) $prototype[ 'bedrooms' ] ?> bedrooms<?= $prototype['bathrooms'] ? ' · ' . (int) $prototype['bathrooms'] . ' bathrooms' : '' ?></span>
        <h3><a href="<?= e( url( 'prototype.php?slug=' . $prototype['slug'] . (!empty($selected_option) ? '&option=' . (int) $selected_option : ''), ), ) ?>"><?= e($prototype['name']) ?></a></h3>
        <a class="text-link" href="<?= e( url( 'prototype.php?slug=' . $prototype['slug'] . (!empty($selected_option) ? '&option=' . (int) $selected_option : ''), ), ) ?>">Explore this design <?= icon('up-right') ?></a>
    </div>
</article>
