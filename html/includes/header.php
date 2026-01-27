<?php
// Start sessie als nog niet gestart
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Afrika Cup' : 'Afrika Cup'; ?></title>

</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏆 AFRIKA CUP</a>
            
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="matches.php">Wedstrijden</a>
                <a href="ticket.php">Mijn tickets</a>
                <div class="nav-right">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php">Mijn Profiel</a>
                        <div class="user-info">
                            <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="admin_panel.php" class="btn-primary">Openstaande aanvragen</a>
                            <?php endif; ?>
                            <a href="logout.php" class="btn-logout">Uitloggen</a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn-primary">Inloggen</a>
                        <a href="register.php" class="btn-primary btn-register">Registreren</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>