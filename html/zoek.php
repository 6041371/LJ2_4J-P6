<?php
$artikelenFile = __DIR__ . '/data/artikelen.json';
$vacaturesFile = __DIR__ . '/data/vacatures.json';

$artikelen = json_decode(file_get_contents($artikelenFile), true) ?: [];
$vacatures = json_decode(file_get_contents($vacaturesFile), true) ?: [];

$q = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'alles';
$doelgroep = $_GET['doelgroep'] ?? 'alles';
$tagFilter = trim($_GET['tag'] ?? '');

$defaultImages = [
    'pagina' => 'img/rest/Home2.jpg',
    'artikel' => 'img/best/IMG_3844.JPG',
    'vacature' => 'img/rest/Onze mensen1.JPG'
];

$validTypes = ['alles', 'paginas', 'artikelen', 'vacatures'];
$validDoelgroepen = ['alles', 'vo', 'po'];

if (!in_array($type, $validTypes, true)) {
    $type = 'alles';
}

if (!in_array($doelgroep, $validDoelgroepen, true)) {
    $doelgroep = 'alles';
}

$manualPageTags = [
    'schoolleidersvannu' => ['schoolleiders', 'vo', 'leiderschap', 'professionalisering'],
    'hoge-verwachtingen' => ['hoge verwachtingen', 'vo', 'po', 'workshop'],
    'leerlingenpopulatie-in-beeld-brengen' => ['leerlingenpopulatie', 'vo', 'po', 'workshop'],
    'audittraining' => ['audits', 'vo', 'po', 'training'],
    'basisvaardigheden' => ['basisvaardigheden', 'vo', 'po'],
    'onderwijskwaliteit' => ['onderwijskwaliteit', 'vo', 'po'],
    'onderwijsresultaten' => ['onderwijsresultaten', 'vo', 'po'],
    'visieontwikkeling' => ['visieontwikkeling', 'vo', 'po'],
    'open-leermaterialen' => ['open leermaterialen', 'vo', 'po'],
    'kwaliteitszorg' => ['kwaliteitszorg', 'vo', 'po'],
    'coaching' => ['coaching', 'vo', 'po'],
    'audits' => ['audits', 'vo', 'po'],
    'onderwijsadvies' => ['onderwijsadvies', 'vo', 'po'],
    'professionalisering' => ['professionalisering', 'vo', 'po'],
    'artikel' => ['artikelen', 'kennisbank', 'onderwijs'],
    'kennisbank' => ['kennisbank', 'downloads', 'onderwijs'],
    'vacatures' => ['vacatures', 'zzp', 'onderwijs']
];

$excludedSlugs = ['header', 'footer', 'admin', 'login', 'quote', 'haakjes', 'dienst', 'expertises', 'zoek', 'artikelen'];

function extractFirstMatch(string $content, string $pattern): string
{
    if (preg_match($pattern, $content, $matches)) {
        return trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    return '';
}

function normalizeTags(array $tags): array
{
    $clean = [];
    foreach ($tags as $tag) {
        $value = trim(mb_strtolower((string) $tag));
        if ($value !== '') {
            $clean[$value] = $value;
        }
    }

    return array_values($clean);
}

function extractFirstImageSrc(string $content): string
{
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
        return trim($matches[1]);
    }

    return '';
}

function resolveImagePath(string $imagePath, string $fallbackImage, string $baseDir): string
{
    $path = trim($imagePath);
    if ($path === '') {
        return $fallbackImage;
    }

    if (preg_match('/^https?:\/\//i', $path) === 1) {
        return $path;
    }

    $normalized = ltrim($path, '/\\');
    $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $normalized);
    $absolute = $baseDir . DIRECTORY_SEPARATOR . $normalized;

    if (is_file($absolute)) {
        return str_replace('\\', '/', $path);
    }

    return $fallbackImage;
}

function inferDoelgroep(array $tags, string $text): string
{
    $haystack = mb_strtolower($text . ' ' . implode(' ', $tags));
    $hasVO = str_contains($haystack, ' vo ') || str_contains($haystack, 'voortgezet') || in_array('vo', $tags, true);
    $hasPO = str_contains($haystack, ' po ') || str_contains($haystack, 'primair') || in_array('po', $tags, true);

    if ($hasVO && $hasPO) {
        return 'beide';
    }

    if ($hasVO) {
        return 'vo';
    }

    if ($hasPO) {
        return 'po';
    }

    return 'alles';
}

function hasTagMatch(array $tags, string $filter): bool
{
    if ($filter === '') {
        return true;
    }

    $filter = mb_strtolower($filter);
    foreach ($tags as $tag) {
        if (str_contains($tag, $filter)) {
            return true;
        }
    }

    return false;
}

function itemMatchesType(array $item, string $type): bool
{
    if ($type === 'alles') {
        return true;
    }

    if ($type === 'paginas') {
        return $item['kind'] === 'pagina';
    }

    if ($type === 'artikelen') {
        return $item['kind'] === 'artikel';
    }

    if ($type === 'vacatures') {
        return $item['kind'] === 'vacature';
    }

    return true;
}

function itemMatchesDoelgroep(array $item, string $doelgroep): bool
{
    if ($doelgroep === 'alles') {
        return true;
    }

    return $item['doelgroep'] === $doelgroep || $item['doelgroep'] === 'beide';
}

function scoreItem(array $item, string $q): int
{
    if ($q === '') {
        return 1;
    }

    $query = mb_strtolower($q);
    $terms = preg_split('/\s+/', $query) ?: [];

    $title = mb_strtolower($item['title']);
    $description = mb_strtolower($item['description']);
    $text = mb_strtolower($item['text']);
    $tagsText = implode(' ', $item['tags']);

    $score = 0;

    if (str_contains($title, $query)) {
        $score += 12;
    }

    if (str_contains($tagsText, $query)) {
        $score += 10;
    }

    if (str_contains($description, $query)) {
        $score += 6;
    }

    if (str_contains($text, $query)) {
        $score += 3;
    }

    foreach ($terms as $term) {
        if (mb_strlen($term) < 2) {
            continue;
        }

        if (str_contains($title, $term)) {
            $score += 4;
        }

        if (str_contains($tagsText, $term)) {
            $score += 3;
        }

        if (str_contains($description, $term)) {
            $score += 2;
        }

        if (str_contains($text, $term)) {
            $score += 1;
        }
    }

    return $score;
}

function buildPageIndex(string $dir, array $excludedSlugs, array $manualPageTags, string $defaultImage): array
{
    $pages = [];
    $files = glob($dir . '/*.php') ?: [];

    foreach ($files as $filePath) {
        $slug = basename($filePath, '.php');

        if (in_array($slug, $excludedSlugs, true)) {
            continue;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            continue;
        }

        $title = extractFirstMatch($content, '/<title>(.*?)<\/title>/is');
        $h1 = extractFirstMatch($content, '/<h1[^>]*>(.*?)<\/h1>/is');
        $description = extractFirstMatch($content, '/<meta\s+name="description"\s+content="([^"]*)"/i');
        $imageSrc = extractFirstImageSrc($content);

        preg_match_all('/<meta\s+name="keywords"\s+content="([^"]*)"/i', $content, $keywordMatches);
        $keywords = [];
        foreach ($keywordMatches[1] ?? [] as $keywordString) {
            $parts = array_map('trim', explode(',', $keywordString));
            $keywords = array_merge($keywords, $parts);
        }

        $overrideTags = $manualPageTags[$slug] ?? [];
        $tags = normalizeTags(array_merge($keywords, $overrideTags));

        // Ensure every indexed page always has tags.
        if ($tags === []) {
            $tags = ['onderwijs', 'today advies'];
        }

        $text = trim(strip_tags($content));
        $mainTitle = $title !== '' ? $title : ($h1 !== '' ? $h1 : ucfirst(str_replace('-', ' ', $slug)));
        $snippet = $description !== '' ? $description : mb_strimwidth(preg_replace('/\s+/', ' ', $text), 0, 220, '...');

        $pages[] = [
            'kind' => 'pagina',
            'title' => $mainTitle,
            'description' => $snippet,
            'url' => $slug === 'index' ? 'index' : $slug,
            'image' => $imageSrc !== '' ? $imageSrc : $defaultImage,
            'tags' => $tags,
            'doelgroep' => inferDoelgroep($tags, $text),
            'text' => $mainTitle . ' ' . $snippet . ' ' . $text
        ];
    }

    return $pages;
}

$allItems = [];

foreach ($artikelen as $i => $a) {
    $title = (string) ($a['titel'] ?? 'Artikel');
    $description = (string) ($a['beschrijving'] ?? ($a['inleiding'] ?? $a['tekst'] ?? ''));
    $tags = normalizeTags($a['tags'] ?? ['artikel']);
    if ($tags === []) {
        $tags = ['artikel', 'onderwijs', 'today advies'];
    }

    $text = implode(' ', [
        $a['titel'] ?? '',
        $a['beschrijving'] ?? '',
        $a['inleiding'] ?? '',
        $a['tekst'] ?? '',
        implode(' ', $tags)
    ]);

    $allItems[] = [
        'kind' => 'artikel',
        'title' => $title,
        'description' => $description,
        'url' => 'artikel?id=' . $i,
        'image' => (string) ($a['afbeelding'] ?? $defaultImages['artikel']),
        'tags' => $tags,
        'doelgroep' => inferDoelgroep($tags, $text),
        'text' => $text
    ];
}

foreach ($vacatures as $i => $v) {
    $title = (string) ($v['titel'] ?? 'Vacature');
    $description = (string) ($v['beschrijving'] ?? '');
    $tags = normalizeTags(array_merge($v['tags'] ?? [], ['vacature']));
    if ($tags === []) {
        $tags = ['vacature', 'onderwijs', 'today advies'];
    }

    $text = implode(' ', [
        $v['titel'] ?? '',
        $v['beschrijving'] ?? '',
        $v['tekst'] ?? '',
        $v['type'] ?? '',
        $v['locatie'] ?? '',
        implode(' ', $tags)
    ]);

    $allItems[] = [
        'kind' => 'vacature',
        'title' => $title,
        'description' => $description,
        'url' => 'vacatures?id=' . $i,
        'image' => (string) ($v['afbeelding'] ?? $defaultImages['vacature']),
        'tags' => $tags,
        'doelgroep' => inferDoelgroep($tags, $text),
        'text' => $text
    ];
}

$pageItems = buildPageIndex(__DIR__, $excludedSlugs, $manualPageTags, $defaultImages['pagina']);
$allItems = array_merge($allItems, $pageItems);

$tagCounts = [];
foreach ($allItems as $item) {
    foreach ($item['tags'] as $tag) {
        if (!isset($tagCounts[$tag])) {
            $tagCounts[$tag] = 0;
        }
        $tagCounts[$tag]++;
    }
}
arsort($tagCounts);
$availableTags = array_slice(array_keys($tagCounts), 0, 24);

$results = [];
$hasSearchInput = $q !== '' || $type !== 'alles' || $doelgroep !== 'alles' || $tagFilter !== '';

if ($hasSearchInput) {
    foreach ($allItems as $item) {
        if (!itemMatchesType($item, $type)) {
            continue;
        }

        if (!itemMatchesDoelgroep($item, $doelgroep)) {
            continue;
        }

        if (!hasTagMatch($item['tags'], $tagFilter)) {
            continue;
        }

        $score = scoreItem($item, $q);
        if ($q !== '' && $score <= 0) {
            continue;
        }

        $item['score'] = $score;
        $results[] = $item;
    }

    usort($results, static function ($a, $b) {
        $scoreSort = $b['score'] <=> $a['score'];
        if ($scoreSort !== 0) {
            return $scoreSort;
        }

        return strcmp($a['title'], $b['title']);
    });
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoeken | TODAY</title>
    <meta name="description" content="Zoek door pagina's, artikelen, vacatures en tags op de TODAY-website.">
    <meta name="keywords" content="zoeken, tags, pagina's, artikelen, vacatures, onderwijs, today advies">
    <link rel="icon" type="image/x-icon" href="img/header/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-[#f7f7fc] text-[#2f2f3a]">

<?php include 'header.php'; ?>

<main class="max-w-6xl mx-auto py-10 px-4 md:px-8">
    <section class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-[#ecebff]">
        <h1 class="text-3xl font-bold text-[#5f5cd6] mb-2">Zoeken op de hele site</h1>
        <p class="text-[#66657a] mb-6">Zoek door pagina's, artikelen, vacatures en tags. Filter daarna op soort, doelgroep (zoals VO) en thema.</p>

        <form method="get" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-3">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($q) ?>"
                    placeholder="Zoek op titel, onderwerp, tag of trefwoord..."
                    class="flex-1 border border-[#d9d7ff] rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#9795F0]"
                >
                <button type="submit" class="bg-[#6b66e4] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#5d57dd] transition-colors">
                    Zoek
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <label class="text-sm font-medium text-[#5a5872]">
                    Soort
                    <select name="type" class="mt-1 w-full border border-[#d9d7ff] rounded-lg px-3 py-2 bg-white">
                        <option value="alles" <?= $type === 'alles' ? 'selected' : '' ?>>Alles</option>
                        <option value="paginas" <?= $type === 'paginas' ? 'selected' : '' ?>>Pagina's</option>
                        <option value="artikelen" <?= $type === 'artikelen' ? 'selected' : '' ?>>Artikelen</option>
                        <option value="vacatures" <?= $type === 'vacatures' ? 'selected' : '' ?>>Vacatures</option>
                    </select>
                </label>

                <label class="text-sm font-medium text-[#5a5872]">
                    Doelgroep
                    <select name="doelgroep" class="mt-1 w-full border border-[#d9d7ff] rounded-lg px-3 py-2 bg-white">
                        <option value="alles" <?= $doelgroep === 'alles' ? 'selected' : '' ?>>Alle doelgroepen</option>
                        <option value="vo" <?= $doelgroep === 'vo' ? 'selected' : '' ?>>VO</option>
                        <option value="po" <?= $doelgroep === 'po' ? 'selected' : '' ?>>PO</option>
                    </select>
                </label>

                <label class="text-sm font-medium text-[#5a5872]">
                    Tag
                    <select name="tag" class="mt-1 w-full border border-[#d9d7ff] rounded-lg px-3 py-2 bg-white">
                        <option value="">Alle tags</option>
                        <?php foreach ($availableTags as $tagOption): ?>
                            <option value="<?= htmlspecialchars($tagOption) ?>" <?= $tagFilter === $tagOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tagOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </form>
    </section>

    <?php if (!$hasSearchInput): ?>
        <p class="text-center text-[#696880] mt-8">Typ een zoekwoord of kies een filter om te starten.</p>
    <?php elseif (empty($results)): ?>
        <p class="text-center text-[#696880] mt-8">Geen resultaten gevonden. Probeer een ander zoekwoord of verwijder een filter.</p>
    <?php else: ?>
        <h2 class="text-xl font-semibold mt-8 mb-4 text-[#403e57]">
            <?= count($results) ?> resultaat<?= count($results) > 1 ? 'en' : '' ?> gevonden
        </h2>

        <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($results as $item): ?>
                <?php
                $label = $item['kind'] === 'artikel' ? 'Artikel' : ($item['kind'] === 'vacature' ? 'Vacature' : 'Pagina');
                $fallback = $defaultImages[$item['kind']] ?? $defaultImages['pagina'];
                $image = resolveImagePath((string) ($item['image'] ?? ''), $fallback, __DIR__);
                ?>
                <article class="bg-white rounded-2xl border border-[#ecebff] overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <a href="<?= htmlspecialchars($item['url']) ?>" class="block no-underline text-inherit">
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-44 object-cover">
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-[#6b66e4]"><?= $label ?></span>
                                <?php if ($item['doelgroep'] === 'vo' || $item['doelgroep'] === 'po' || $item['doelgroep'] === 'beide'): ?>
                                    <span class="text-xs px-2 py-1 rounded-full bg-[#f2f1ff] text-[#5f5cd6]">
                                        <?= $item['doelgroep'] === 'beide' ? 'VO + PO' : strtoupper($item['doelgroep']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-lg font-bold mb-2 leading-snug"><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="text-sm text-[#6a6980] mb-3">
                                <?= htmlspecialchars(mb_strimwidth($item['description'], 0, 140, '...')) ?>
                            </p>

                            <div class="flex flex-wrap gap-1.5 mb-3">
                                <?php foreach (array_slice($item['tags'], 0, 4) as $chip): ?>
                                    <span class="text-[11px] px-2 py-1 rounded-full bg-[#f6e6ef] text-[#6a6980]">
                                        <?= htmlspecialchars($chip) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <p class="text-sm font-semibold text-[#6b66e4]">Bekijk resultaat →</p>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
