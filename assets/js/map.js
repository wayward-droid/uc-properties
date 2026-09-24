'use strict';
// Leaflet is local. Only the base-map tiles are requested from OpenStreetMap.
document.querySelectorAll('[data-estate-map]').forEach((explorer) => {
    let initialized = false;
    function initializeMap() {
        if (initialized || !window.L) return;
        initialized = true;
        const canvas = explorer.querySelector('[data-map-canvas]');
        const title = explorer.querySelector('[data-map-title]');
        const status = explorer.querySelector('[data-map-status]');
        const external = explorer.querySelector('[data-map-external]');
        const reset = explorer.querySelector('[data-map-reset]');
        const error = explorer.querySelector('[data-map-error]');
        const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
        canvas.replaceChildren();
        const map = L.map(canvas, {
            scrollWheelZoom: false,
            zoomControl: false,
            tap: true,
            zoomAnimation: !reduced(),
            fadeAnimation: !reduced(),
        });
        L.control.zoom({ position: 'topright' }).addTo(map);
        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors',
        }).addTo(map);
        let successfulTiles = 0;
        tiles.on('tileload', () => {
            successfulTiles++;
            error.hidden = true;
        });
        tiles.on('tileerror', () => {
            if (!successfulTiles) error.hidden = false;
        });
        const items = [...explorer.querySelectorAll('[data-map-item]')];
        const points = [];
        const markers = [];
        function selectItem(item, marker) {
            items.forEach((el) => {
                el.classList.toggle('is-selected', el === item);
                el.querySelector('[data-map-select]').setAttribute(
                    'aria-pressed',
                    String(el === item),
                );
            });
            markers.forEach((m) => m.getElement()?.classList.toggle('is-selected', m === marker));
            title.textContent = item.dataset.mapLabel;
            const hasPoint = item.dataset.lat !== '' && item.dataset.lng !== '';
            status.textContent =
                item.dataset.mapExact === '1'
                    ? 'Estate location supplied by UC Properties. Confirm your meeting point before visiting.'
                    : hasPoint
                      ? 'Approximate area centre. Contact our team for the estate entrance and exact plot directions.'
                      : 'Exact directions are available from the team. This map shows the wider Abuja area.';
            if (hasPoint) {
                const point = [Number(item.dataset.lat), Number(item.dataset.lng)];
                map.flyTo(point, item.dataset.mapExact === '1' ? 15 : 12, {
                    animate: !reduced(),
                    duration: 1.1,
                });
                external.href = `https://www.openstreetmap.org/?mlat=${point[0]}&mlon=${point[1]}#map=13/${point[0]}/${point[1]}`;
            } else {
                map.setView([8.995, 7.35], 11, { animate: !reduced() });
                external.href = 'https://www.openstreetmap.org/#map=11/8.995/7.35';
            }
        }
        items.forEach((item, index) => {
            const button = item.querySelector('[data-map-select]');
            button.disabled = false;
            let marker;
            if (item.dataset.lat !== '' && item.dataset.lng !== '') {
                const point = [Number(item.dataset.lat), Number(item.dataset.lng)];
                points.push(point);
                const label = document.createElement('span');
                const number = document.createElement('b');
                number.textContent = String(index + 1).padStart(2, '0');
                label.append(number, document.createTextNode(item.dataset.mapLabel));
                marker = L.marker(point, {
                    icon: L.divIcon({
                        className: 'estate-map-marker',
                        html: label,
                        iconSize: null,
                        iconAnchor: [40, 20],
                    }),
                    title:
                        item.dataset.mapLabel +
                        (item.dataset.mapExact === '1'
                            ? ' — supplied estate location'
                            : ' — approximate area'),
                    keyboard: true,
                }).addTo(map);
                marker.on('click', () => selectItem(item, marker));
                markers.push(marker);
            }
            button.addEventListener('click', () => selectItem(item, marker));
        });
        function showAll() {
            map.stop();
            if (points.length > 1)
                map.fitBounds(points, { padding: [60, 65], maxZoom: 11, animate: !reduced() });
            else if (points.length === 1) map.setView(points[0], 12, { animate: !reduced() });
            else map.setView([8.995, 7.35], 11, { animate: !reduced() });
            items.forEach((el) => {
                el.classList.remove('is-selected');
                el.querySelector('button').setAttribute('aria-pressed', 'false');
            });
            markers.forEach((m) => m.getElement()?.classList.remove('is-selected'));
            title.textContent = 'Explore Abuja';
            status.textContent =
                'Area markers are approximate neighbourhood centres, not estate entrances or plot boundaries.';
            external.href = 'https://www.openstreetmap.org/#map=11/8.995/7.35';
        }
        reset.hidden = false;
        reset.addEventListener('click', showAll);
        showAll();
        if ('ResizeObserver' in window)
            new ResizeObserver(() => map.invalidateSize({ pan: false })).observe(canvas);
        explorer.dataset.mapReady = 'true';
        // A grid/map view can reveal this widget after its initial layout.
        explorer.addEventListener('map:visible', () => {
            map.invalidateSize();
            showAll();
        });
    }
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    initializeMap();
                    observer.disconnect();
                }
            },
            { rootMargin: '180px' },
        );
        observer.observe(explorer);
    } else initializeMap();
});
