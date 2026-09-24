<?php
require __DIR__ . '/includes/bootstrap.php';
$title = 'Find your place in Abuja';
$locations = rows('SELECT * FROM locations ORDER BY name');
$where = ['e.published=1'];
$params = [];
$optionWhere = ['o.estate_id=e.id', 'o.published=1'];
$optionParams = [];
if (ctype_digit(input('location')) && input('location') !== '0') {
    $where[] = 'e.location_id=?';
    $params[] = (int) input('location');
}
if (in_array(input('availability'), ['enquire', 'available', 'sold_out', 'coming_soon'], true)) {
    $where[] = 'e.availability=?';
    $params[] = input('availability');
}
if (in_array(input('type'), ['land', 'home'], true)) {
    $optionWhere[] = 'o.kind=?';
    $optionParams[] = input('type');
}
if (is_numeric(input('budget')) && (float) input('budget') > 0) {
    $optionWhere[] = 'o.price_verified=1 AND o.outright_price IS NOT NULL AND o.outright_price<=?';
    $optionParams[] = (float) input('budget');
}
if (is_numeric(input('size')) && (float) input('size') > 0) {
    $optionWhere[] = 'o.size_sqm=?';
    $optionParams[] = (float) input('size');
}
if (input('type') === 'home' && ctype_digit(input('bedrooms')) && (int) input('bedrooms') > 0) {
    $optionWhere[] = 'o.bedrooms=?';
    $optionParams[] = (int) input('bedrooms');
}
if (count($optionWhere) > 2) {
    $where[] =
        'EXISTS (SELECT 1 FROM property_options o WHERE ' . implode(' AND ', $optionWhere) . ')';
    $params = array_merge($params, $optionParams);
}
$whereSQL = implode(' AND ', $where);
$total = (int) row('SELECT COUNT(*) AS n FROM estates e WHERE ' . $whereSQL, $params)['n'];
$page = max(1, min((int) input('page', '1'), max(1, (int) ceil($total / 9))));
$offset = ($page - 1) * 9;
$sort =
    [
        'name' => 'e.name ASC',
        'newest' => 'e.created_at DESC,e.id DESC',
        'price' => 'lowest_price IS NULL,lowest_price ASC,e.name',
    ][input('sort', 'name')] ?? 'e.name ASC';
$estates = rows(
    'SELECT e.*,l.name AS location_name,(SELECT MIN(o.outright_price) FROM property_options o WHERE o.estate_id=e.id AND o.published=1 AND o.price_verified=1) AS lowest_price FROM estates e JOIN locations l ON l.id=e.location_id WHERE ' .
        $whereSQL .
        ' ORDER BY ' .
        $sort .
        ' LIMIT 9 OFFSET ' .
        $offset,
    $params,
);
$hasMap = true;
include __DIR__ . '/includes/header.php';
?>
<main id="main">
<section class="page-heading">
<div class="container wide">
<p class="eyebrow">OUR ESTATES</p>
<h1>Your next address<br>starts here.</h1>
<p>Explore our Abuja estates by location and plot options. Speak with the team for current availability and a full price breakdown.</p>
</div>
</section>
<section class="section container wide">
<form class="filters" action="<?= e( url('estates.php'), ) ?>" method="get" aria-label="Filter estates">
    <div>
<label for="location">Location</label>
<select id="location" name="location">
<option value="">All locations</option><?php foreach (
        $locations
        as $l
    ): ?><option value="<?= (int) $l['id'] ?>"<?= selected(input('location'), $l['id']) ?>><?= e( $l['name'], ) ?></option><?php endforeach; ?></select>
</div>
    <div>
<label for="type-filter">Property type</label>
<select id="type-filter" name="type">
<option value="">All types</option>
<option value="land"<?= selected( input('type'), 'land', ) ?>>Land</option>
<option value="home"<?= selected( input('type'), 'home', ) ?>>Completed home</option>
</select>
</div>
    <div>
<label for="budget">Maximum budget (₦)</label>
<input id="budget" name="budget" type="number" min="0" max="999999999999" step="1000" placeholder="Any budget" value="<?= e( input('budget'), ) ?>">
</div>
    <div>
<label for="size">Plot size</label>
<select id="size" name="size">
<option value="">Any size</option><?php foreach (
        rows('SELECT DISTINCT size_sqm FROM property_options WHERE published=1 ORDER BY size_sqm')
        as $s
    ): ?><option value="<?= (int) $s['size_sqm'] ?>"<?= selected( input('size'), (int) $s['size_sqm'], ) ?>><?= number_format((float) $s['size_sqm']) ?> m²</option><?php endforeach; ?></select>
</div>
    <div>
<label for="availability">Availability</label>
<select id="availability" name="availability">
<option value="">All availability</option><?php foreach (
        [
            'enquire' => 'Enquire with team',
            'available' => 'Available',
            'coming_soon' => 'Coming soon',
            'sold_out' => 'Sold out',
        ]
        as $v => $label
    ): ?><option value="<?= e($v) ?>"<?= selected(input('availability'), $v) ?>><?= e( $label, ) ?></option><?php endforeach; ?></select>
</div>
    <div id="bedroom-filter-field"<?= input('type') !== 'home' ? ' hidden' : '' ?>>
<label for="bedrooms">Bedrooms</label>
<select id="bedrooms" name="bedrooms">
<option value="">Any bedrooms</option><?php for (
    $i = 1;
    $i <= 8;
    $i++
): ?><option<?= selected(input('bedrooms'), $i) ?>><?= $i ?></option><?php endfor; ?></select>
</div>
    <div>
<label for="sort">Sort by</label>
<select id="sort" name="sort">
<option value="name"<?= selected( input('sort', 'name'), 'name', ) ?>>Estate name</option>
<option value="newest"<?= selected( input('sort'), 'newest', ) ?>>Newest first</option>
<option value="price"<?= selected( input('sort'), 'price', ) ?>>Confirmed price: low to high</option>
</select>
</div>
    <div class="filter-actions">
<button type="submit" class="btn btn-small">Apply filters <?= icon( 'arrow', ) ?></button>
<a href="<?= e(url('estates.php')) ?>">Reset filters</a>
</div>
</form>
<?php if (
    input('budget') !== ''
): ?><p class="note">Budget results include only options with a confirmed outright price. Contact us to discuss estates whose pricing is awaiting confirmation.</p><?php endif; ?>
<div class="results-header">
<p><?= $total ?> estate<?= $total === 1 ? '' : 's' ?> <?= count( $where, ) > 1 ? 'matching your search' : 'to explore' ?></p>
<?php if ($estates): ?><div class="view-switch" data-view-switch hidden aria-label="Estate results view"><button type="button" data-view="grid" aria-pressed="true"><?= icon('home') ?> Grid view</button><button type="button" data-view="map" aria-pressed="false"><?= icon('pin') ?> Map view</button></div><?php endif; ?>
</div>
<?php if ($estates): ?><div data-results-panel="grid"><div class="card-grid results-grid"><?php foreach ($estates as $estate) {
    include __DIR__ . '/includes/estate-card.php';
} ?></div></div><div data-results-panel="map" hidden><?php $mapEstates = $estates; include __DIR__ . '/includes/estate-map.php'; ?></div><?php else: ?><div class="empty-state">
<h2>Let’s widen the search.</h2>
<p>No published estates match these filters. Try another location or contact the team for options that suit your plans.</p>
<a class="btn" href="<?= e( url('estates.php'), ) ?>">Show all estates</a>
</div><?php endif; ?>
<?php if ($total > 9): ?><nav class="pagination" aria-label="Estate pages"><?php for (
    $p = 1;
    $p <= ceil($total / 9);
    $p++
): ?><a href="<?= e( url('estates.php?' . http_build_query(array_merge($_GET, ['page' => $p]))), ) ?>"<?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a><?php endfor; ?></nav><?php endif; ?>
</section><?php include __DIR__ . '/includes/cta.php'; ?></main><?php include __DIR__ .
    '/includes/footer.php'; ?>
