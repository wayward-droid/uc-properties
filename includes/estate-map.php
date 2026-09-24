<?php
// Area centres are orientation aids, never substituted into the estate database.
// Exact estate coordinates, when supplied by staff, take precedence.
$mapAreas = [
    'idu' => ['name' => 'Idu area', 'lat' => 9.06885, 'lng' => 7.35296],
    'kabusa' => ['name' => 'Kabusa area', 'lat' => 8.94536, 'lng' => 7.45285],
    'kuje' => ['name' => 'Kuje area', 'lat' => 8.8796, 'lng' => 7.2272],
];
$mapItems = [];
foreach ($mapEstates as $mapEstate) {
    $point = null;
    $exact = is_numeric($mapEstate['latitude']) && is_numeric($mapEstate['longitude'])
        && abs((float) $mapEstate['latitude']) <= 90 && abs((float) $mapEstate['longitude']) <= 180;
    if ($exact) {
        $point = ['name' => $mapEstate['name'], 'lat' => (float) $mapEstate['latitude'], 'lng' => (float) $mapEstate['longitude']];
    } else {
        foreach ($mapAreas as $match => $area) {
            if (str_contains(strtolower($mapEstate['location_name']), $match)) { $point = $area; break; }
        }
    }
    $mapItems[] = ['estate' => $mapEstate, 'point' => $point, 'exact' => $exact];
}
?>
<div class="estate-explorer" data-estate-map>
    <div class="map-sidebar">
        <div class="map-sidebar-heading"><span><?= icon('pin') ?> ABUJA, NIGERIA</span><strong><?= count($mapItems) ?> estate<?= count($mapItems) === 1 ? '' : 's' ?> to explore</strong></div>
        <div class="map-estate-list">
            <?php foreach ($mapItems as $i => $item): $mapEstate = $item['estate']; $point = $item['point']; ?>
            <article class="map-estate" data-map-item data-lat="<?= e($point['lat'] ?? '') ?>" data-lng="<?= e($point['lng'] ?? '') ?>" data-map-label="<?= e($point['name'] ?? $mapEstate['location_name']) ?>" data-map-exact="<?= $item['exact'] ? '1' : '0' ?>">
                <button type="button" data-map-select aria-pressed="false" disabled>
                    <img src="<?= e(picture($mapEstate)) ?>" alt="" width="86" height="76" loading="lazy">
                    <span><small><?= e($mapEstate['location_name']) ?></small><strong><?= e($mapEstate['name']) ?></strong><span><?= $item['exact'] ? 'Supplied estate location' : ($point ? 'Explore the area' : 'Directions on request') ?></span></span>
                    <?= icon('arrow') ?>
                </button>
                <a class="map-estate-link" href="<?= e(url('estate.php?slug=' . $mapEstate['slug'])) ?>">View estate details <?= icon('up-right') ?></a>
            </article>
            <?php endforeach; ?>
        </div>
        <a class="map-help" href="<?= e(inspection_whatsapp()) ?>"><?= icon('calendar') ?><span>Want to see it in person?<strong>Book an inspection <?= icon('arrow') ?></strong></span></a>
    </div>
    <div class="map-display">
        <div class="map-toolbar"><span><?= icon('pin') ?> <strong data-map-title>Explore Abuja</strong></span><button type="button" data-map-reset hidden>Show all areas</button></div>
        <div class="map-canvas" data-map-canvas aria-label="Interactive map of estate areas in Abuja">
            <div class="map-fallback"><strong>Discover our Abuja locations</strong><p>Use the estate links to explore each location. The interactive map needs JavaScript and an internet connection.</p><a href="https://www.openstreetmap.org/#map=11/8.995/7.35" target="_blank" rel="noopener">Open Abuja map <?= icon('up-right') ?></a></div>
        </div>
        <p class="map-tile-error" data-map-error hidden role="status">Map tiles are unavailable. You can still select an estate or use the map link below.</p>
        <div class="map-bottom"><p data-map-status aria-live="polite">Area markers are approximate neighbourhood centres, not estate entrances or plot boundaries.</p><a data-map-external href="https://www.openstreetmap.org/#map=11/8.995/7.35" target="_blank" rel="noopener">Open map <?= icon('up-right') ?></a></div>
    </div>
</div>
