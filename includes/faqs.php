<div class="faq-list">
    <?php foreach ($faqs as $faq): ?>
    <details class="faq-item">
        <summary>
            <span><?= e( $faq['question'], ) ?></span>
            <span class="faq-plus" aria-hidden="true">+</span>
        </summary>
        <div class="faq-answer"><p><?= paragraphs( $faq['answer'], ) ?></p></div>
    </details>
    <?php endforeach; ?>
</div>
