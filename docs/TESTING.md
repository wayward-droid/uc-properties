# Verification report — 22 September 2026

Verified with PHP 8.3.6, MariaDB 10.11.7 and Chromium 153 in a local Linux test environment. The application ran in a subfolder on port 8088, with MySQL on a separate non-default port. The release contains no test credentials, private configuration, test enquiries or uploaded fixtures.

## Redesign and interaction checks

- Every public route remains a separate PHP page. All existing public pages loaded successfully.
- Homepage, listings, estate details, design browsing, inspection and contact layouts fit 320, 375, 768, 1024 and 1440 pixel viewports without horizontal overflow. The staff overview also fit those widths.
- Carousel next/previous controls, keyboard arrows, optional timed playback and Pause worked. Only the current image/caption is active. Reduced-motion preferences disabled entrance, slide, gallery, FAQ and page effects.
- Mobile menu toggle, close button, Escape and focus trapping worked. Floating contact controls did not overlap at 320/375 pixels.
- Homepage search submitted its real location/type filters to estates.php. Listing grid/map switching retained the same filtered results.
- Real OpenStreetMap tiles, visible attribution, all three approximate area markers, selection highlighting, animated map movement and reset were exercised. Each estate detail retained its own map. Mobile map and horizontal estate choices fit their container.
- The browser's extracted test binary did not have the environment proxy CA in its trust store. A QA-only network adapter fetched real tile bytes using the runtime's trusted HTTPS transport and supplied them unchanged to the browser. No simulated map images, adapter, proxy settings or tile cache are shipped.
- A deliberate tile-network failure displayed the fallback notice while retaining estate and external map links. With JavaScript disabled, navigation, search, estate links, native FAQs and the map fallback remained usable.
- Direct inspection links used 2348065741674 and encoded the correct estate, plot and approved design. Both form submit buttons generated a PHP 303 redirect to the correct WhatsApp draft with the completed details and request reference.
- Form redirects were intercepted before leaving the test environment. No WhatsApp message was sent and no personal data was transmitted during testing. The visitor must tap Send in WhatsApp; the website cannot confirm that they did so.
- A native browser view-transition experiment produced abort exceptions during rapid navigation. It was replaced with simple CSS page entrance transitions. The final interaction run, including navigation across all public page types and staff sign-in, reported no JavaScript exceptions.

## Preserved PHP/MySQL behaviour

- Every application PHP file passed syntax checks. All three application JavaScript files passed syntax checks.
- Location/type/size/budget filters, budget empty states and completed-home bedroom filters worked. A confirmed-price fixture matched the combined query.
- Unapproved design associations stayed hidden. Approved estate/plot/design selections appeared in the WhatsApp draft and remained supported by the inspection page.
- Valid enquiries and inspections saved their existing staff records before the WhatsApp handoff. Optional email could be blank. Staff could review the complete record, change its status and save private notes. Script-like message text was escaped.
- Missing CSRF tokens and invalid form data were rejected. Protected staff pages rejected unauthenticated access. Administrator sign-in and POST logout worked.
- Staff could create a location/estate, upload an image, publish and edit an estate, set verified pricing, and approve compatibility. A disguised executable upload was rejected. Estate deletion required the explicit DELETE confirmation and removed the public route.
- The original release had also checked PDF floor-plan uploads, gallery changes, published FAQs/articles, shared contact settings, invalid property associations, incomplete verified prices, and simulated mail failure preserving the request. Those backend paths were not replaced by the visual redesign.

## Scope and remaining environment checks

This is a runnable source update, not a deployment to the company website. Physical Windows/XAMPP, Safari/Firefox, actual WhatsApp-app handoff on iOS/Android, live mail delivery, and Apache-specific .htaccess enforcement remain checks on the user's installation. No SQL migration or re-import is needed for this update. Keep the existing private config and uploads.

Prices, estate entrances, availability, development status, title statements, compatibility approvals and legal text still need the company's review. The new map is an area guide wherever exact coordinates are absent; it does not assert parcel boundaries. Map tiles require an internet connection.

Preview images in this folder show the company content, not QA fixtures.
