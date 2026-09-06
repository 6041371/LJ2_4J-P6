<section class="article-hero">
<div class="hero-img">
<img src="<?= htmlspecialchars($artikel['afbeelding'] ?? 'img/rest/placeholder.png') ?>" alt="">
</div>
<div class="hero-content">
<h1><?= htmlspecialchars($artikel['titel']) ?></h1>
<div class="meta">Tags: <?= htmlspecialchars(implode(', ', $artikel['tags'] ?? [])) ?></div>
</div>
</section>
<section class="article-body">
<?= nl2br(htmlspecialchars($artikel['tekst'])) ?>
</section>