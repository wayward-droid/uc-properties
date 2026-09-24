<?php require __DIR__ . '/includes/bootstrap.php';
$article = row('SELECT * FROM articles WHERE published=1 AND slug=?', [input('slug')]);
if (!$article) {
    not_found();
}
$title = $article['title'];
$description = $article['summary'];
include __DIR__ . '/includes/header.php';
?>
<main id="main">
    <article class="section container reading">
        <a class="text-link mb-4" href="<?= e( url('updates.php'), ) ?>">Back to updates</a>
        <p class="eyebrow"><?= e($article['category']) ?> · <?= date( 'j F Y', strtotime($article['created_at']), ) ?></p>
        <h1><?= e($article['title']) ?></h1>
        <p class="lead my-4"><?= e( $article['summary'], ) ?></p>
        <?php if ($article['cover_image']): ?>
        <img src="<?= e( asset($article['cover_image']), ) ?>" alt="<?= e($article['title']) ?>" class="mb-4" />
        <?php endif; ?>
        <div><?= paragraphs( $article['body'], ) ?></div>
    </article>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
