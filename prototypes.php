<?php
require __DIR__ . '/includes/bootstrap.php';
$title = 'Discover what you could build';
$estates = published_estates();
$eid = ctype_digit(input('estate')) ? (int) input('estate') : 0;
$selected_option = ctype_digit(input('option')) ? (int) input('option') : 0;
if (!array_filter($estates, fn($e) => (int) $e['id'] === $eid)) {
    $eid = 0;
}
$options = $eid
    ? rows('SELECT * FROM property_options WHERE estate_id=? AND published=1 ORDER BY size_sqm', [
        $eid,
    ])
    : [];
if (!array_filter($options, fn($o) => (int) $o['id'] === $selected_option)) {
    $selected_option = 0;
}
if ($selected_option) {
    $prototypes = rows(
        'SELECT p.* FROM prototypes p JOIN compatibility c ON c.prototype_id=p.id WHERE p.published=1 AND c.option_id=? AND c.approved=1 ORDER BY p.bedrooms',
        [$selected_option],
    );
} elseif ($eid) {
    $prototypes = rows(
        'SELECT DISTINCT p.* FROM prototypes p JOIN compatibility c ON c.prototype_id=p.id JOIN property_options o ON o.id=c.option_id WHERE p.published=1 AND c.approved=1 AND o.published=1 AND o.estate_id=? ORDER BY p.bedrooms',
        [$eid],
    );
} else {
    $prototypes = rows('SELECT * FROM prototypes WHERE published=1 ORDER BY bedrooms,id');
}
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">BUILDING DESIGNS</p>
            <h1>Make space for your vision.</h1>
            <p>
                Explore proposed homes. Choose an estate and plot to see designs specifically
                approved for that option.
            </p>
        </div>
    </section>
    <section class="container wide section">
        <nav class="journey" aria-label="Your property journey">
            <span>
                01
                <strong>Choose an estate</strong>
            </span>
            <span>→</span>
            <span>02 Choose a plot</span>
            <span>→</span>
            <span>03 Explore designs</span>
            <span>→</span>
            <span>04 Request a visit</span>
        </nav>
        <form action="<?= e( url('prototypes.php'), ) ?>" method="get" class="filters">
            <div>
                <label for="estate-select">Your estate</label>
                <select name="estate" id="estate-select" data-autosubmit>
                    <option value="">All building designs</option>
                    <?php foreach (
    $estates
    as $e
): ?>
                    <option value="<?= (int) $e['id'] ?>" <?= selected($eid, $e['id']) ?>><?= e( $e['name'], ) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="plot-select">Your plot option</label>
                <select name="option" id="plot-select">
                    <option value="">All approved plot options</option>
                    <?php foreach (
    $options
    as $o
): ?>
                    <option value="<?= (int) $o['id'] ?>" <?= selected($selected_option, $o['id']) ?>><?= e( $o['name'], ) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-actions">
                <button class="btn btn-small" type="submit">Show designs <?= icon( 'arrow', ) ?></button>
                <a href="<?= e(url('prototypes.php')) ?>">Reset</a>
            </div>
        </form>
        <?php if (
    $prototypes
): ?>
        <p class="note">
            Architectural renderings illustrate proposed designs. Land, construction, and
            completed-home offers are priced separately unless an approved quotation expressly
            includes them.
        </p>
        <div class="prototype-grid"><?php foreach (
    $prototypes
    as $prototype
) {
    include __DIR__ . '/includes/prototype-card.php';
} ?></div>
        <?php else: ?>
        <div class="empty-state">
            <h2>Let’s find the right fit together.</h2>
            <p>
                No designs have been approved for this selection yet. The team can discuss suitable
                options and confirm the details with you.
            </p>
            <a class="btn" href="<?= e( inspection_whatsapp((int) $eid, (int) $selected_option), ) ?>">Discuss this plot <?= icon('up-right') ?></a>
        </div>
        <?php endif; ?>
    </section>
    <?php include __DIR__ . '/includes/cta.php'; ?>
</main>
<?php include __DIR__ .
    '/includes/footer.php'; ?>
