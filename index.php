<?php
require __DIR__ . '/includes/bootstrap.php';
$allEstates = published_estates();
$estates = array_slice($allEstates, 0, 3);
$locations = rows('SELECT l.*,COUNT(e.id) AS estate_count FROM locations l JOIN estates e ON e.location_id=l.id AND e.published=1 GROUP BY l.id ORDER BY l.id');
$sizes = rows('SELECT DISTINCT o.size_sqm FROM property_options o JOIN estates e ON e.id=o.estate_id WHERE o.published=1 AND e.published=1 ORDER BY o.size_sqm');
$prototypes = rows('SELECT * FROM prototypes WHERE published=1 ORDER BY id LIMIT 2');
$faqs = rows('SELECT * FROM faqs WHERE published=1 ORDER BY sort_order,id LIMIT 4');
$articles = rows('SELECT * FROM articles WHERE published=1 ORDER BY created_at DESC LIMIT 3');
$testimonials = rows('SELECT * FROM testimonials WHERE published=1 AND approved=1 ORDER BY id DESC LIMIT 3');
$hasMap = true;
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="property-hero" aria-label="Discover UC Properties" aria-roledescription="carousel" data-hero>
        <div class="hero-slides">
            <?php if ($estates): foreach ($estates as $i => $slide): ?>
            <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" data-slide aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>">
                <img src="<?= e(picture($slide)) ?>" alt="<?= e($slide['name']) ?> — <?= e($slide['image_kind']) ?>" width="1920" height="1080" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
            </div>
            <?php endforeach; else: ?>
            <div class="hero-slide is-active" data-slide><img src="<?= e(url('assets/images/hero.png')) ?>" alt="UC Properties architectural rendering" width="1920" height="980" fetchpriority="high"></div>
            <?php endif; ?>
        </div>
        <div class="container wide property-hero-content">
            <div class="hero-intro">
                <p class="hero-kicker"><span></span> YOUR NEXT CHAPTER STARTS IN ABUJA</p>
                <h1>Find your place. <br> <span>Build your future.</span></h1>
                <p>Land, inspiring home designs, and room for what’s next.  <br> Discover your possibilities with UC Properties.</p>
                <a class="hero-explore" href="#estates">Explore our estates <span><?= icon('arrow') ?></span></a>
            </div>
            <div class="hero-lower">
                <a class="hero-map-link" href="#explore-map"><?= icon('pin') ?> Explore Abuja on the map</a>
                <?php if ($estates): ?>
                <div class="hero-property" aria-live="off">
                    <?php foreach ($estates as $i => $slide): ?>
                    <a class="hero-property-link" data-slide-caption <?= $i ? 'hidden' : '' ?> href="<?= e(url('estate.php?slug=' . $slide['slug'])) ?>">
                        <small><?= e($slide['location_name']) ?>, Abuja</small>
                        <strong><?= e($slide['name']) ?> <?= icon('up-right') ?></strong>
                        <span><?= $slide['image_kind'] === 'rendering' ? 'Architectural rendering' : 'Property photograph' ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (count($estates) > 1): ?>
                <div class="hero-controls" hidden>
                    <div class="slide-count"><span data-slide-number>01</span><span>/ <?= str_pad((string) count($estates), 2, '0', STR_PAD_LEFT) ?></span></div>
                    <button type="button" data-slide-prev aria-label="Previous estate"><?= icon('arrow') ?></button>
                    <button type="button" data-slide-next aria-label="Next estate"><?= icon('arrow') ?></button>
                    <button class="slide-play" type="button" data-slide-play aria-label="Play estate slideshow" aria-pressed="false">Play</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="search-wrap container wide">
        <form class="property-search" method="get" action="<?= e(url('estates.php')) ?>" aria-label="Find an estate">
            <div class="search-topline">
                <fieldset class="search-tabs">
                    <legend class="visually-hidden">Property type</legend>
                    <label><input type="radio" name="type" value="" checked><span>All estates</span></label>
                    <label><input type="radio" name="type" value="land"><span><?= icon('land') ?> Land</span></label>
                    <label><input type="radio" name="type" value="home"><span><?= icon('home') ?> Completed homes</span></label>
                </fieldset>
                <span class="search-caption">A place that fits your plans.</span>
            </div>
            <div class="search-fields">
                <div><label for="home-location"><?= icon('pin') ?> Location</label><select id="home-location" name="location"><option value="">Anywhere in Abuja</option><?php foreach ($locations as $location): ?><option value="<?= (int) $location['id'] ?>"><?= e($location['name']) ?></option><?php endforeach; ?></select></div>
                <div><label for="home-size"><?= icon('land') ?> Plot size</label><select id="home-size" name="size"><option value="">Any plot size</option><?php foreach ($sizes as $size): ?><option value="<?= (int) $size['size_sqm'] ?>"><?= number_format((float) $size['size_sqm']) ?> m²</option><?php endforeach; ?></select></div>
                <div><label for="home-budget">₦ &nbsp; Maximum budget</label><select id="home-budget" name="budget"><option value="">Any budget</option><option value="10000000">Up to ₦10 million</option><option value="25000000">Up to ₦25 million</option><option value="50000000">Up to ₦50 million</option><option value="100000000">Up to ₦100 million</option></select></div>
                <button class="btn btn-gold" type="submit">Find property <?= icon('arrow') ?></button>
            </div>
            <p class="search-budget-note" data-budget-note hidden>Budget searches show options with confirmed pricing. For other prices, speak with our team.</p>
        </form>
    </div>

    <div class="quick-paths container wide">
        <a href="<?= e(url('estates.php?type=land')) ?>"><span><?= icon('land') ?></span><div><strong>A plot for your plans</strong><small>Explore land across our estates</small></div><?= icon('up-right') ?></a>
        <a href="<?= e(url('prototypes.php')) ?>"><span><?= icon('home') ?></span><div><strong>A vision for your home</strong><small>Discover proposed building designs</small></div><?= icon('up-right') ?></a>
        <a href="<?= e(inspection_whatsapp()) ?>"><span><?= icon('calendar') ?></span><div><strong>See it for yourself</strong><small>Arrange a visit with our team</small></div><?= icon('up-right') ?></a>
    </div>

    <section id="estates" class="section container wide estate-selection">
        <div class="section-heading reveal">
            <div><p class="eyebrow">EXPLORE OUR ESTATES</p><h2>Your search. <br> <span>A little closer to home.</span></h2></div>
            <a class="btn btn-outline" href="<?= e(url('estates.php')) ?>">View all estates <?= icon('up-right') ?></a>
        </div>
        <?php if ($estates): ?>
        <div class="card-grid"><?php foreach ($estates as $estate) { include __DIR__ . '/includes/estate-card.php'; } ?></div>
        <?php else: ?>
        <div class="empty-state"><h3>Your next property starts with a conversation.</h3><a class="btn" href="<?= e(url('contact.php')) ?>">Talk to the team</a></div>
        <?php endif; ?>
    </section>

    <section id="explore-map" class="map-section section">
        <div class="container wide">
            <div class="section-heading reveal"><div><p class="eyebrow">GET TO KNOW THE NEIGHBOURHOOD</p><h2>Find your corner of Abuja.</h2></div><p>Every location tells a different story. <br> Select an estate to explore its area.</p></div>
            <?php $mapEstates = $allEstates; include __DIR__ . '/includes/estate-map.php'; ?>
        </div>
    </section>

    <?php if ($prototypes): ?>
    <section class="section container wide design-selection">
        <div class="section-heading reveal"><div><p class="eyebrow">IMAGINE THE POSSIBILITIES</p><h2>A little inspiration. <br> A home that feels like you.</h2></div><div><p>Explore our proposed home designs. <br> We’ll help confirm the right fit for your plot.</p><a class="text-link" href="<?= e(url('prototypes.php')) ?>">All building designs <?= icon('up-right') ?></a></div></div>
        <div class="prototype-grid"><?php foreach ($prototypes as $prototype) { include __DIR__ . '/includes/prototype-card.php'; } ?></div>
    </section>
    <?php endif; ?>

    <section class="company-story container wide">
        <div class="story-image reveal"><img src="<?= e(url('assets/images/villa-valore-5bed-detached.jpg')) ?>" alt="Proposed five-bedroom detached home at Villa Valoré" width="1000" height="600" loading="lazy"><span class="image-caption">Proposed design · Architectural rendering</span><div class="story-stamp"><?= icon('home') ?><span>More than property. <br> <strong>Your next possibility.</strong></span></div></div>
        <div class="story-copy reveal"><p class="eyebrow">THIS IS UC PROPERTIES</p><h2>Big plans. <br> A personal approach.</h2><p>Your property journey should start with a conversation. We help you explore land, understand your options, and plan the next step with confidence.</p><div class="story-services"><a href="<?= e(url('services.php#real-estate')) ?>">Real estate <?= icon('up-right') ?></a><a href="<?= e(url('services.php#investment')) ?>">Investment guidance <?= icon('up-right') ?></a><a href="<?= e(url('services.php#project-management')) ?>">Project management <?= icon('up-right') ?></a></div><a class="btn" href="<?= e(url('about.php')) ?>">Get to know us <?= icon('arrow') ?></a></div>
    </section>

    <section class="section container wide journey-section">
        <div class="section-heading reveal"><div><p class="eyebrow">YOUR NEXT MOVE, MADE SIMPLE</p><h2>From browsing to being there.</h2></div><a class="text-link" href="<?= e(inspection_whatsapp()) ?>">Let’s plan your visit <?= icon('up-right') ?></a></div>
        <div class="journey-cards">
            <article class="reveal"><span class="step-index">01</span><div class="step-icon"><?= icon('pin') ?></div><h3>Find your place</h3><p>Explore locations, compare plot sizes, and discover an estate that suits your plans.</p></article>
            <article class="reveal"><span class="step-index">02</span><div class="step-icon"><?= icon('calendar') ?></div><h3>Take a closer look</h3><p>Request an inspection. Our team will get in touch to arrange the details of your visit.</p></article>
            <article class="reveal"><span class="step-index">03</span><div class="step-icon"><?= icon('check') ?></div><h3>Make an informed move</h3><p>Review current pricing, documentation, and payment terms with us before deciding.</p></article>
        </div>
    </section>

    <?php if ($articles): ?>
    <section class="section container wide"><div class="section-heading"><h2>From our estates.</h2><a class="text-link" href="<?= e(url('updates.php')) ?>">All updates <?= icon('up-right') ?></a></div><div class="card-grid"><?php foreach ($articles as $article): ?><article class="article-card reveal"><p class="eyebrow"><?= e($article['category']) ?></p><h3><a href="<?= e(url('article.php?slug=' . $article['slug'])) ?>"><?= e($article['title']) ?></a></h3><p><?= e($article['summary']) ?></p></article><?php endforeach; ?></div></section>
    <?php endif; ?>
    <?php if ($testimonials): ?>
    <section class="section testimonials"><div class="container wide"><p class="eyebrow">IN OUR CLIENTS’ WORDS</p><div class="card-grid"><?php foreach ($testimonials as $t): ?><figure><blockquote>“<?= e($t['quote']) ?>”</blockquote><figcaption><?= e($t['name']) ?> · <?= e($t['attribution']) ?></figcaption></figure><?php endforeach; ?></div></div></section>
    <?php endif; ?>
    <?php if ($faqs): ?>
    <section class="section faq-section"><div class="container wide faq-grid"><div><p class="eyebrow">LET’S CLEAR A FEW THINGS UP</p><h2>Good questions. <br> Helpful answers.</h2><p>Everything starts with the right information. <br> Here are a few things you might be wondering.</p><a class="text-link" href="<?= e(url('faq.php')) ?>">More questions, answered <?= icon('up-right') ?></a></div><?php include __DIR__ . '/includes/faqs.php'; ?></div></section>
    <?php endif; ?>
    <?php include __DIR__ . '/includes/cta.php'; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
