<?php
// Session starten als nog niet actief
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = 'Vul alle velden in';
    } else {
        $user = new User();

        if ($user->login($_POST['email'], $_POST['password'])) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Inloggen mislukt of account nog niet goedgekeurd';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Inloggen - Afrika Cup</title>
</head>
<body>
    <?php include_once 'includes/header.php';?>
    
    <div class="login-container">
        <div class="login-form">
            <h2>Inloggen</h2>
            <h3>Log in met je e-mailadres en wachtwoord</h3>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>❌ Fout:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">E-mailadres <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Bijv. jouw@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord <span class="required">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Jouw wachtwoord" required>
                </div>

                <button type="submit" class="btn-login">🔐 Inloggen</button>
            </form>

            <div class="register-link">
                Nog geen account? <a href="register.php">Registreer hier</a>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php';?>
</body>
</html>
