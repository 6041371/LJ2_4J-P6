<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

/* ================== DATA BESTANDEN ================== */
$artikelenFile = __DIR__ . '/data/artikelen.json';
$vacaturesFile = __DIR__ . '/data/vacatures.json';

if (!file_exists($artikelenFile)) file_put_contents($artikelenFile, '[]');
if (!file_exists($vacaturesFile)) file_put_contents($vacaturesFile, '[]');

$artikelen = json_decode(file_get_contents($artikelenFile), true) ?: [];
$vacatures = json_decode(file_get_contents($vacaturesFile), true) ?: [];

/* ================== RANDOM AFBEELDING ================== */
function getRandomImage($dir = 'img/rest') {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) return '';
    $files = glob($path . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (!$files) return '';
    return str_replace(__DIR__ . '/', '', $files[array_rand($files)]);
}

/* ================== VERWIJDEREN ================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $type = $_GET['type'] ?? 'artikel';

    if ($type === 'artikel' && isset($artikelen[$id])) {
        array_splice($artikelen, $id, 1);
        file_put_contents($artikelenFile, json_encode($artikelen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    if ($type === 'vacature' && isset($vacatures[$id])) {
        array_splice($vacatures, $id, 1);
        file_put_contents($vacaturesFile, json_encode($vacatures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    header('Location: admin.php');
    exit;
}

/* ================== TOEVOEGEN ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type'] ?? 'artikel';
    $afbeeldingPath = '';

    /* upload */
    if (!empty($_FILES['afbeelding']['name'])) {
        $ext = strtolower(pathinfo($_FILES['afbeelding']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            if (!is_dir(__DIR__.'/assets/images')) {
                mkdir(__DIR__.'/assets/images', 0755, true);
            }
            $afbeeldingPath = 'assets/images/' . time() . '_' . preg_replace('/[^a-z0-9_.-]/i','', $_FILES['afbeelding']['name']);
            move_uploaded_file($_FILES['afbeelding']['tmp_name'], __DIR__.'/'.$afbeeldingPath);
        }
    } elseif (!empty($_POST['afbeelding_url'])) {
        $afbeeldingPath = trim($_POST['afbeelding_url']);
    } else {
        $afbeeldingPath = getRandomImage();
    }

    /* artikel */
    if ($type === 'artikel') {
        $artikelen[] = [
            'titel' => trim($_POST['titel']),
            'beschrijving' => trim($_POST['beschrijving']),
            'inleiding' => str_replace(["\r\n", "\r"], "\n", $_POST['inleiding']),
            'tekst' => str_replace(["\r\n", "\r"], "\n", $_POST['tekst']),
            'afbeelding' => $afbeeldingPath,
            'tags' => array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')))
        ];
        file_put_contents($artikelenFile, json_encode($artikelen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /* vacature */
    if ($type === 'vacature') {
        $vacatures[] = [
            'titel' => trim($_POST['titel']),
            'type' => trim($_POST['dienstverband']),
            'locatie' => trim($_POST['locatie']),
            'beschrijving' => trim($_POST['beschrijving']),
            'tekst' => str_replace(["\r\n", "\r"], "\n", $_POST['tekst']), // Markdown toegestaan
            'afbeelding' => $afbeeldingPath,
            'tags' => array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')))
        ];
        file_put_contents($vacaturesFile, json_encode($vacatures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin</title>
<link rel="stylesheet" href="css/style.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white font-sans">
<?php include 'header.php'; ?>

<main class="max-w-4xl mx-auto p-8 text-center">
<h1 class="text-3xl font-bold mb-4">Admin</h1>
<a href="login.php?logout=1" class="text-purple-600">Uitloggen</a>

<div class="flex justify-center gap-4 my-6">
<button onclick="showTab('artikel')" class="bg-purple-600 text-white px-4 py-2 rounded">Artikelen</button>
<button onclick="showTab('vacature')" class="bg-purple-600 text-white px-4 py-2 rounded">Vacatures</button>
</div>

<!-- ARTIKEL -->
<div id="artikel-tab">
<h2 class="text-2xl font-semibold mb-4">Nieuw artikel</h2>
<form method="post" enctype="multipart/form-data" class="space-y-4">
<input type="hidden" name="type" value="artikel">

<input name="titel" placeholder="Titel" class="border p-2 w-full" required>
<textarea name="beschrijving" placeholder="Korte beschrijving" class="border p-2 w-full"></textarea>
<textarea name="inleiding" placeholder="Inleiding (vet bovenaan)" class="border p-2 w-full"></textarea>

<textarea name="tekst" rows="10" class="border p-2 w-full"
placeholder="Gebruik *vet* of _cursief_ voor opmaak. Markdown toegestaan."></textarea>

<input name="tags" placeholder="tags, komma, gescheiden" class="border p-2 w-full">
<input type="file" name="afbeelding" class="border p-2 w-full">
<input name="afbeelding_url" placeholder="Of afbeelding URL" class="border p-2 w-full">

<button class="bg-purple-600 text-white px-6 py-2 rounded">Opslaan</button>
</form>

<ul class="mt-8 space-y-2">
<?php 
require_once __DIR__.'/includes/Parsedown.php';
$Parsedown = new Parsedown();

foreach ($artikelen as $i => $a): ?>
<li class="border-b py-2 text-left">
<strong><?= htmlspecialchars($a['titel']) ?></strong>
— <a href="artikel.php?id=<?= $i ?>" class="text-purple-600" target="_blank">Bekijk</a>
— <a href="?delete=<?= $i ?>" class="text-red-600">Verwijderen</a>
<div class="text-sm mt-1">
    <?= $Parsedown->text($a['inleiding']) ?>
</div>
</li>
<?php endforeach; ?>
</ul>
</div>

<!-- VACATURE -->
<div id="vacature-tab" style="display:none">
<h2 class="text-2xl font-semibold mb-4">Nieuwe vacature</h2>
<form method="post" enctype="multipart/form-data" class="space-y-4">
<input type="hidden" name="type" value="vacature">

<input name="titel" placeholder="Titel" class="border p-2 w-full">
<input name="dienstverband" placeholder="Dienstverband" class="border p-2 w-full">
<input name="locatie" placeholder="Locatie" class="border p-2 w-full">

<textarea name="beschrijving" class="border p-2 w-full"></textarea>
<textarea name="tekst" rows="8" class="border p-2 w-full"
placeholder="Gebruik *vet* of _cursief_ voor opmaak. Markdown toegestaan."></textarea>

<input type="file" name="afbeelding" class="border p-2 w-full">
<input name="afbeelding_url" class="border p-2 w-full">

<button class="bg-purple-600 text-white px-6 py-2 rounded">Opslaan</button>
</form>

<ul class="mt-8 space-y-2">
<?php foreach ($vacatures as $i => $v): ?>
<li class="border-b py-2 text-left">
<strong><?= htmlspecialchars($v['titel']) ?></strong>
— <a href="vacature.php?id=<?= $i ?>" class="text-purple-600" target="_blank">Bekijk</a>
— <a href="?delete=<?= $i ?>&type=vacature" class="text-red-600">Verwijderen</a>
<div class="text-sm mt-1">
    <?= $Parsedown->text($v['beschrijving']) ?>
</div>
</li>
<?php endforeach; ?>
</ul>
</div>

<script>
function showTab(tab){
document.getElementById('artikel-tab').style.display = tab === 'artikel' ? 'block':'none';
document.getElementById('vacature-tab').style.display = tab === 'vacature' ? 'block':'none';
}
</script>

</main>
<?php include 'footer.php'; ?>
</body>
</html>
