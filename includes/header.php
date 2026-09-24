<?php
$active = basename($_SERVER['SCRIPT_NAME']);
$isHome = $active === 'index.php';
$title = $title ?? 'Land, homes and possibilities in Abuja';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($description ?? 'Explore UC Properties estates in Abuja, discover building designs, and arrange an inspection with our team.') ?>">
    <title><?= e($title) ?> | UC Properties Limited</title>
    <link rel="icon" href="<?= e(url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preload" href="<?= e(url('assets/fonts/manrope.ttf')) ?>" as="font" type="font/ttf" crossorigin>
    <link rel="stylesheet" href="<?= e(url('assets/vendor/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <?php if (!empty($hasMap)): ?>
    <link rel="stylesheet" href="<?= e(url('assets/vendor/leaflet/leaflet.css')) ?>">
    <script src="<?= e(url('assets/vendor/leaflet/leaflet.js')) ?>" defer></script>
    <script src="<?= e(url('assets/js/map.js')) ?>" defer></script>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(url('assets/css/design.css')) ?>">
    <script src="<?= e(url('assets/js/main.js')) ?>" defer></script>
    <script src="<?= e(url('assets/js/experience.js')) ?>" defer></script>
</head>
<body class="<?= $isHome ? 'home-page' : 'inner-page' ?>">
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <nav class="navigation container wide" aria-label="Main navigation">
            <a class="brand" href="<?= e(url()) ?>">
                <img class="logo logo-black" src="<?= e(url('assets/images/uc-logo.png')) ?>" alt="UC Properties Limited — home" width="400" height="110">
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-menu" aria-label="Open navigation"><?= icon('menu') ?></button>
            <div id="main-menu" class="nav-panel">
                <button class="menu-close" type="button" aria-label="Close navigation"><?= icon('close') ?></button>
                <a <?= $isHome ? 'aria-current="page"' : '' ?> href="<?= e(url()) ?>">Home</a>
                <a <?= in_array($active, ['estates.php', 'estate.php']) ? 'aria-current="page"' : '' ?> href="<?= e(url('estates.php')) ?>">Our estates</a>
                <a <?= in_array($active, ['prototypes.php', 'prototype.php']) ? 'aria-current="page"' : '' ?> href="<?= e(url('prototypes.php')) ?>">Building designs</a>
                <a <?= $active === 'about.php' ? 'aria-current="page"' : '' ?> href="<?= e(url('about.php')) ?>">About us</a>
                <a <?= $active === 'contact.php' ? 'aria-current="page"' : '' ?> href="<?= e(url('contact.php')) ?>">Contact</a>
                <a class="btn btn-small" href="<?= e(inspection_whatsapp()) ?>">Book an inspection <?= icon('up-right') ?></a>
            </div>
        </nav>
    </header>
    <noscript>
        <nav class="container py-3" aria-label="Navigation without JavaScript">
            <a href="<?= e(url('estates.php')) ?>">Estates</a> ·
            <a href="<?= e(url('prototypes.php')) ?>">Building designs</a> ·
            <a href="<?= e(url('about.php')) ?>">About</a> ·
            <a href="<?= e(url('faq.php')) ?>">FAQs</a> ·
            <a href="<?= e(url('contact.php')) ?>">Contact</a>
        </nav>
    </noscript>
    <?php if (!empty($_SESSION['flash'])): ?>
    <div class="container"><div class="notice success" role="status"><?= e($_SESSION['flash']) ?></div></div>
    <?php unset($_SESSION['flash']); endif; ?>
