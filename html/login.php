<?php
session_start();

$correct_user = 'admin';
$correct_pass = 'geheim123'; // verander dit naar jouw eigen wachtwoord!

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === $correct_user && $pass === $correct_pass) {
        $_SESSION['logged_in'] = true;
        header('Location: admin');
        exit;
    } else {
        $error = 'Onjuiste gebruikersnaam of wachtwoord.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen</title>
  <link rel="icon" type="image/x-icon" href="img/header/favicon.png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">
  <form method="post" class="bg-white shadow-md rounded-lg p-8 w-80">
    <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
    <?php if($error): ?>
      <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <label class="block mb-2 font-semibold">Gebruikersnaam</label>
    <input type="text" name="username" required class="border p-2 w-full mb-4 rounded">
    
    <label class="block mb-2 font-semibold">Wachtwoord</label>
    <input type="password" name="password" required class="border p-2 w-full mb-6 rounded">
    
    <button type="submit" class="bg-purple-600 text-white py-2 px-4 rounded w-full">Inloggen</button>
  </form>
</body>
</html>
