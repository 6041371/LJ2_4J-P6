<?php
// Session starten als nog niet actief
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/Supporter.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['naam']) ||
        empty($_POST['email']) ||
        empty($_POST['password']) ||
        empty($_POST['adres'])
    ) {
        $error = 'Niet alle verplichte velden ingevuld';
    } else {
        $supporter = new Supporter();

        // Check of email al bestaat
        if ($supporter->emailExists($_POST['email'])) {
            $error = 'E-mail is al gekoppeld aan een account';
        } else if ($supporter->register(
            $_POST['naam'],
            $_POST['password'],
            $_POST['email'],
            $_POST['adres']
        )) {
            $success = 'Registratie succesvol! Je aanvraag wacht op goedkeuring.';
        } else {
            $error = 'Er is een fout opgetreden bij de registratie';
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
    <title>Registreren - Afrika Cup</title>
</head>
<body>
    <?php include_once 'includes/header.php';?>
    
    <div class="register-container">
        <div class="register-form">
            <h2>Registreren als Supporter</h2>
            <h3>Vul je gegevens in en wacht op goedkeuring</h3>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>❌ Fout:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <div class="alert alert-success">
                        <strong>✅ Succes!</strong> <?php echo htmlspecialchars($success); ?>
                    </div>
                    <p>Je ontvangt een e-mail zodra je aanvraag is goedgekeurd.</p>
                    <a href="index.php">Terug naar Home</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="naam">Volledige Naam <span class="required">*</span></label>
                        <input type="text" id="naam" name="naam" placeholder="Bijv. Jan de Vries" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mailadres <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Bijv. jouw@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Wachtwoord <span class="required">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Minimaal 8 karakters" required>
                    </div>

                    <div class="form-group">
                        <label for="adres">Adres <span class="required">*</span></label>
                        <textarea id="adres" name="adres" placeholder="Straat, huisnummer, postcode, plaats" required></textarea>
                    </div>

                    <button type="submit" class="btn-register">Registreren</button>
                </form>

                <div class="login-link">
                    Al een account? <a href="login.php">Inloggen hier</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'includes/footer.php';?>
</body>
</html>
