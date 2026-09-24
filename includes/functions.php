<?php
declare(strict_types=1);
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function url(string $path = ''): string
{
    global $config;
    return rtrim($config['base_url'], '/') . '/' . ltrim($path, '/');
}
function setting(string $key, string $fallback = ''): string
{
    global $settings;
    return $settings[$key] ?? $fallback;
}
function rows(string $sql, array $params = []): array
{
    global $db;
    $s = $db->prepare($sql);
    $s->execute($params);
    return $s->fetchAll();
}
function row(string $sql, array $params = []): ?array
{
    return rows($sql, $params)[0] ?? null;
}
function run(string $sql, array $params = []): void
{
    global $db;
    $s = $db->prepare($sql);
    $s->execute($params);
}
function go(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit();
}
function csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function verify_csrf(): void
{
    if (
        !is_string($_POST['csrf'] ?? null) ||
        !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])
    ) {
        http_response_code(419);
        exit('Your session expired. Reload the form and try again.');
    }
}
function flash(string $text): void
{
    $_SESSION['flash'] = $text;
}
function input(string $key, string $fallback = ''): string
{
    $v = $_POST[$key] ?? ($_GET[$key] ?? $fallback);
    return is_string($v) ? trim($v) : $fallback;
}
function selected(mixed $a, mixed $b): string
{
    return (string) $a === (string) $b ? ' selected' : '';
}
function money(mixed $amount): string
{
    return $amount === null || $amount === ''
        ? 'Contact us for current pricing'
        : '₦' . number_format((float) $amount, 0);
}
function phone(): string
{
    return setting('phone', '+2348065741674');
}
function whatsapp(
    string $message = 'Hello UC Properties, I would like to find out more about your estates.',
): string {
    return 'https://wa.me/' .
        preg_replace('/\D/', '', setting('whatsapp', '2348065741674')) .
        '?text=' .
        rawurlencode($message);
}
// Inspection links open WhatsApp immediately, with only published, matching selections.
function inspection_whatsapp(int $estateId = 0, int $optionId = 0, int $prototypeId = 0): string
{
    $lines = ['Hello UC Properties, I would like to request an inspection.'];
    $estate = $estateId ? row('SELECT id,name,slug FROM estates WHERE id=? AND published=1', [$estateId]) : null;
    if ($estate) {
        $lines[] = 'Estate: ' . $estate['name'];
        $option = $optionId ? row('SELECT id,name FROM property_options WHERE id=? AND estate_id=? AND published=1', [$optionId, $estateId]) : null;
        if ($option) {
            $lines[] = 'Plot option: ' . $option['name'];
            $prototype = $prototypeId ? row('SELECT p.name FROM prototypes p JOIN compatibility c ON c.prototype_id=p.id WHERE p.id=? AND c.option_id=? AND c.approved=1 AND p.published=1', [$prototypeId, $optionId]) : null;
            if ($prototype) $lines[] = 'Building design: ' . $prototype['name'];
        }
        $lines[] = url('estate.php?slug=' . $estate['slug']);
    }
    $lines[] = 'Please help me arrange a visit and confirm the meeting point.';
    return whatsapp(implode("\n", $lines));
}
function asset(?string $path): string
{
    return url($path ?: 'assets/images/hero.png');
}
function picture(array $record): string
{
    return asset($record['cover_image'] ?? null);
}
function content_text(?string $value): string
{
    return strtr($value ?? '', [
        '{{phone}}' => setting('phone_display'),
        '{{email}}' => setting('email'),
        '{{address}}' => setting('address'),
    ]);
}
function paragraphs(?string $value): string
{
    return nl2br(e(content_text($value)));
}
function tag(string $value): string
{
    return ucwords(str_replace('_', ' ', $value));
}
function published_estates(): array
{
    return rows(
        'SELECT e.*, l.name AS location_name FROM estates e JOIN locations l ON l.id=e.location_id WHERE e.published=1 ORDER BY e.featured DESC,e.name',
    );
}
function not_found(): never
{
    http_response_code(404);
    $title = 'Page not found';
    include ROOT . '/includes/header.php';
    echo '<main class="container section"><p class="eyebrow">404</p><h1>Let’s find your way home.</h1><p>This page is no longer available.</p><a class="btn" href="' .
        e(url()) .
        '">Back to home</a></main>';
    include ROOT . '/includes/footer.php';
    exit();
}
function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        go('admin/login.php');
    }
    $admin = row('SELECT id FROM admins WHERE id=? AND active=1', [$_SESSION['admin_id']]);
    if (!$admin || time() - ($_SESSION['admin_seen'] ?? 0) > 1800) {
        unset($_SESSION['admin_id']);
        go('admin/login.php');
    }
    $_SESSION['admin_seen'] = time();
}
// Database-backed rate limits survive a new browser session. No raw IP addresses are stored.
function rate_limit(string $action, int $max, int $window): bool
{
    global $config, $db;
    $key = hash_hmac(
        'sha256',
        $action . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'cli'),
        $config['app_secret'],
    );
    $start = date('Y-m-d H:i:s', intdiv(time(), $window) * $window);
    run(
        'INSERT INTO rate_limits (bucket, window_start, hits) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE hits=IF(window_start=VALUES(window_start),hits+1,1),window_start=VALUES(window_start)',
        [$key, $start],
    );
    return (int) row('SELECT hits FROM rate_limits WHERE bucket=?', [$key])['hits'] <= $max;
}
function icon(string $name, string $class = ''): string
{
    $paths = [
        'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'up-right' => '<path d="M6 18 18 6M6 6h12v12"/>',
        'pin' =>
            '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'phone' => '<path d="m7 3 3 5-2 2c1 3 3 5 6 6l2-2 5 3-1 4C10 22 2 14 3 4Z"/>',
        'whatsapp' =>
            '<path d="M21 11.5A9 9 0 0 1 8 20l-5 1 1-5A9 9 0 1 1 21 11.5Z"/><path d="m8 7 2 3-1 1c1 2 2 3 4 3l1-1 3 2c-3 4-11-3-9-8Z"/>',
        'land' => '<path d="m3 7 7-4 11 4v10l-7 4-11-4V7Zm7-4v10l11 4M3 7l11 4v10m-4-8 4-2"/>',
        'home' => '<path d="m3 10 9-7 9 7v11H3V10Zm6 11v-8h6v8"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 5 9 8 9-8"/>',
        'calendar' =>
            '<rect x="3" y="5" width="18" height="16" rx="1"/><path d="M7 2v6m10-6v6M3 11h18"/>',
    ];
    return '<svg class="icon ' .
        e($class) .
        '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
        ($paths[$name] ?? $paths['arrow']) .
        '</svg>';
}
