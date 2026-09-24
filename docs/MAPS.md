# Map implementation and location sources

The website uses Leaflet 1.9.4, bundled locally with its BSD licence, and the standard OpenStreetMap raster tile service. The Manrope font is bundled locally under its SIL Open Font License.

## Position accuracy

The supplied estate records did not contain latitude or longitude. The following centres provide neighbourhood context only. The interface says these are approximate area centres, not estate entrances or plot boundaries. No inferred coordinates are saved into the estate database.

| Map label | Latitude | Longitude | Source reviewed 21 September 2026 |
| --- | --- | --- | --- |
| Idu area | 9.06885 | 7.35296 | https://mapcarta.com/17023560 (GeoNames 2338941) |
| Kabusa area | 8.94536 | 7.45285 | https://mapcarta.com/37372728 (GeoNames 12513525) |
| Kuje area | 8.8796 | 7.2272 | https://mapcarta.com/Kuje (OpenStreetMap node 3615289126) |

These places correspond to the company-source location names Idu (Gousa), Sheretti/Kabusa, and Kuje. Area association is for orientation; it is not verification of a specific parcel. The location dictionary is visible in `includes/estate-map.php`. Published estate latitude/longitude supplied by staff take precedence and are labelled accordingly. Unrecognised areas without coordinates do not receive invented pins.

## Behaviour

- The homepage shows all published estates. The listing page's grid/map switch uses the same filtered, paginated result set. Estate detail pages show their own location.
- Select an estate card or keyboard-focusable marker to highlight it and move the map. Show all areas restores the overview.
- Browser geolocation is not used. Scroll-wheel zoom is off to keep ordinary page scrolling predictable; zoom controls, drag, touch and keyboard interaction are available.
- Map code loads locally. Tiles load when the map approaches the viewport and follow the browser's normal HTTP cache handling. No tiles are bundled, prefetched or downloaded for offline use in the delivered project.
- Visible OpenStreetMap attribution is retained. The content security policy permits images from `https://tile.openstreetmap.org`; scripts and fonts remain local. The normal cross-origin referrer policy is retained.
- If JavaScript or tile loading is unavailable, property links and an external map link remain usable.

## Production use

Review the tile service policy before launch or substantial traffic growth: https://operations.osmfoundation.org/policies/tiles/

Leaflet documentation: https://leafletjs.com/examples/quick-start/
OpenStreetMap attribution: https://www.openstreetmap.org/copyright
Map tiles are a best-effort external service. If changing providers in `assets/js/map.js`, also update attribution and the image domain in `includes/bootstrap.php`.

## Design references

The refreshed design was informed by the large property-image slider at https://propertylistings.mshelhomes.com/ and the rounded navigation, prominent search, property cards, scroll reveals and image hover transitions at https://www.findnigeriaproperties.com/. UC Properties branding, imagery and content are retained; reference-company listings, prices and testimonials were not copied.
