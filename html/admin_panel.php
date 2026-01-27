<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'classes/Beheerder.php';

$beheerder = new Beheerder();
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supporter_id = $_POST['supporter_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($supporter_id && $action) {
        if ($action === 'approve') {
            if ($beheerder->approveSupporter($supporter_id)) {
                $fan_id = $beheerder->assignFanId($supporter_id);
                $message = "✅ Supporter goedgekeurd! Fan-ID: <strong>$fan_id</strong>";
                $message_type = 'success';
            } else {
                $message = "❌ Fout bij goedkeuring";
                $message_type = 'error';
            }
        } elseif ($action === 'reject') {
            if ($beheerder->rejectSupporter($supporter_id)) {
                $message = "❌ Supporter afgewezen";
                $message_type = 'warning';
            } else {
                $message = "❌ Fout bij afwijzing";
                $message_type = 'error';
            }
        }
    }
}

$pending_supporters = $beheerder->getPendingSupporters();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Panel - Afrika Cup</title>
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-header">
            <h1>⚙️ Admin Panel</h1>
            <p>Beheer supporterregistraties en toekenningen</p>
        </div>

        <div class="admin-links">
            <a href="manage_referees.php" class="btn-admin-link">👨‍⚖️ Scheidsrechters Beheren</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3>⏳ In afwachting</h3>
                <div class="number"><?php echo count($pending_supporters); ?></div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-button active" onclick="switchTab('pending')">
                ⏳ In afwachting (<?php echo count($pending_supporters); ?>)
            </button>
        </div>

        <div id="pending" class="tab-content active">
            <h2>Openstaande Aanvragen</h2>
            <?php if (count($pending_supporters) > 0): ?>
                <?php foreach ($pending_supporters as $supporter): ?>
                    <div class="supporter-card">
                        <div class="supporter-info">
                            <h3><?php echo htmlspecialchars($supporter['naam']); ?></h3>
                            <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($supporter['email']); ?></p>
                            <p><strong>📍 Adres:</strong> <?php echo htmlspecialchars($supporter['adres']); ?></p>
                        </div>
                        <div class="supporter-actions">
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="supporter_id" value="<?php echo $supporter['supporter_id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-approve">✅ Goedkeuren</button>
                            </form>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="supporter_id" value="<?php echo $supporter['supporter_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn-reject" onclick="return confirm('Weet je zeker?');">❌ Afwijzen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>Geen openstaande aanvragen</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <script>
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
