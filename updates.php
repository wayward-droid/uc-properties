<?php require __DIR__ . '/includes/bootstrap.php';
$title = 'Updates from UC Properties';
$total = (int) row('SELECT COUNT(*) AS n FROM articles WHERE published=1')['n'];
$page = max(1, min((int) input('page', '1'), max(1, (int) ceil($total / 9))));
$articles = rows(
    'SELECT * FROM articles WHERE published=1 ORDER BY created_at DESC LIMIT 9 OFFSET ' .
        ($page - 1) * 9,
);
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <section class="page-heading">
        <div class="container wide">
            <p class="eyebrow">NEWS & PROJECT UPDATES</p>
            <h1>
                Stay close to
                <br />
                what’s happening.
            </h1>
            <p>Updates, estate news, and practical information from the UC Properties team.</p>
        </div>
    </section>
    <section class="section container wide">
        <?php
if ($articles): ?>
        <div class="card-grid">
            <?php foreach (
    $articles
    as $a
): ?>
            <article class="article-card">
                <?php if ($a['cover_image']): ?>
                <img src="<?= e( asset($a['cover_image']), ) ?>" alt="<?= e($a['title']) ?>" loading="lazy" />
                <?php endif; ?>
                <p class="eyebrow"><?= e( $a['category'], ) ?></p>
                <h2><a href="<?= e(url('article.php?slug=' . $a['slug'])) ?>"><?= e( $a['title'], ) ?></a></h2>
                <p><?= e($a['summary']) ?></p>
                <a class="text-link" href="<?= e( url('article.php?slug=' . $a['slug']), ) ?>">Read the update <?= icon( 'arrow', ) ?></a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h2>Let’s keep you in the picture.</h2>
            <p>
                There are no published updates here yet. Ask the team for the latest information
                about your chosen estate.
            </p>
            <a class="btn" href="<?= e( url('contact.php'), ) ?>">Ask for an update</a>
        </div>
        <?php endif;
if ($total > 9): ?>
        <nav class="pagination" aria-label="Update pages">
            <?php for (
    $p = 1;
    $p <= ceil($total / 9);
    $p++
): ?>
            <a href="<?= e(url('updates.php?page=' . $p)) ?>" <?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif;
?>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
