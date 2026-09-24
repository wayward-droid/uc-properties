<?php require_once __DIR__ . '/entities.php';
$adminName = row('SELECT name FROM admins WHERE id=?', [$_SESSION['admin_id']])['name'];
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <title><?= e( $title ?? 'Studio', ) ?> | UC Properties administration</title>
        <link rel="icon" href="<?= e( url('assets/images/favicon.svg'), ) ?>" />
        <link rel="stylesheet" href="<?= e( url('assets/vendor/bootstrap.min.css'), ) ?>" />
        <link rel="stylesheet" href="<?= e( url('assets/css/style.css'), ) ?>" />
        <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>" />
        <script src="<?= e( url('assets/js/main.js'), ) ?>" defer></script>
    </head>
    <body class="admin-body">
        <a class="skip-link" href="#main">Skip to content</a>
        <header class="admin-top">
            <a href="<?= e( url('admin/'), ) ?>">
                <img
                    class="logo logo-white"
                    src="<?= e( url('assets/images/uc-logo.png'), ) ?>"
                    alt="UC Properties administration"
                    width="400"
                    height="110"
                />
            </a>
            <div>
                <a href="<?= e( url(), ) ?>">View website <?= icon('up-right') ?></a>
                <form method="post" action="<?= e( url('admin/logout.php'), ) ?>">
                    <?= csrf() ?>
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </header>
        <div class="admin-shell">
            <aside class="admin-nav">
                <p class="eyebrow">YOUR WORKSPACE</p>
                <a href="<?= e( url('admin/'), ) ?>">Overview</a>
                <a href="<?= e( url('admin/requests.php'), ) ?>">Enquiries & inspections</a>
                <?php foreach ($entities as $key => $entityInfo): ?>
                <a href="<?= e( url('admin/content.php?entity=' . $key), ) ?>" <?= ($entity ?? '') === $key ? ' aria-current="page"' : '' ?>><?= e( $entityInfo['label'], ) ?></a>
                <?php endforeach; ?>
                <a href="<?= e( url('admin/settings.php'), ) ?>">Company settings</a>
            </aside>
            <main id="main" class="admin-main">
                <div class="admin-heading">
                    <div>
                        <p class="eyebrow">UC PROPERTIES · ADMINISTRATION</p>
                        <h1><?= e( $title ?? 'Overview', ) ?></h1>
                    </div>
                    <span><?= e($adminName) ?></span>
                </div>
                <?php if (
    !empty($_SESSION['flash'])
): ?>
                <div class="notice success" role="status"><?= e($_SESSION['flash']) ?></div>
                <?php unset(
    $_SESSION['flash'],
);endif; ?>
            </main>
        </div>
    </body>
</html>
