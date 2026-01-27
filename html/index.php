<?php
// Session starten als nog niet actief
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afrika Cup - Officieel Ticketsysteem</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🏆 Welkom bij Afrika Cup</h1>
            <p>Het officiële platform voor kaartjesverkoop van de Afrika Cup voetbaltournament</p>
            <div class="hero-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">📊 Mijn Profiel</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">🔐 Inloggen</a>
                    <a href="register.php" class="btn btn-secondary">✍️ Registreer Gratis</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Waarom Kiezen voor Afrika Cup?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon">🎟️</span>
                    <h3>Eenvoudig Kaartjes Kopen</h3>
                    <p>Registreer, wacht op goedkeuring en bestel veilig tickets voor alle wedstrijden.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">👤</span>
                    <h3>Geverifieerde Supporters</h3>
                    <p>Alle supporters worden handmatig goedgekeurd door onze beheerders voor veiligheid.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🔒</span>
                    <h3>Veilig & Betrouwbaar</h3>
                    <p>Jouw gegevens zijn veilig opgeslagen en beschermd met geavanceerde encryptie.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">⚽</span>
                    <h3>Officiële Wedstrijden</h3>
                    <p>Volg alle officiële Afrika Cup wedstrijden in één centraal overzicht.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📱</span>
                    <h3>Altijd Toegang</h3>
                    <p>Bekijk je profiel en tickets op elk apparaat, overal en altijd, 24/7.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🆔</span>
                    <h3>Fan-ID Systeem</h3>
                    <p>Alleen goedgekeurde supporters krijgen toegang tot exclusive tickets.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>32</h3>
                    <p>Wedstrijden</p>
                </div>
                <div class="stat-item">
                    <h3>6</h3>
                    <p>Stadions</p>
                </div>
                <div class="stat-item">
                    <h3>24</h3>
                    <p>Landen</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Officieel</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Klaar om te starten?</h2>
            <p>Registreer nu als supporter en ontvang updates over alle wedstrijden</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-secondary">✍️ Registreer Gratis</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-secondary">Ga naar Profiel</a>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>