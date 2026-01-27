<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/User.php';
require_once 'classes/Database.php';

$user = new User();
$profile = $user->getUser($_SESSION['user_id']);
$role = $_SESSION['role'] ?? 'supporter';
$email = $_SESSION['email'] ?? '';

$edit_message = '';
$edit_message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
    $naam = $_POST['naam'] ?? '';
    $email_input = $_POST['email'] ?? '';
    
    if (empty($naam) || empty($email_input)) {
        $edit_message = "❌ Naam en e-mail zijn verplicht";
        $edit_message_type = 'error';
    } else {
        if ($user->editProfile($_SESSION['user_id'], $naam, $email_input)) {
            $_SESSION['username'] = $naam;
            $_SESSION['email'] = $email_input;
            $edit_message = "✅ Gegevens succesvol gewijzigd";
            $edit_message_type = 'success';
            $profile = $user->getUser($_SESSION['user_id']);
        } else {
            $edit_message = "❌ Fout bij wijzigen gegevens";
            $edit_message_type = 'error';
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
    <title>Mijn Dashboard - Afrika Cup</title>
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>👤 Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <p>Dit is je persoonlijk dashboard</p>
        </div>

        <?php if ($edit_message): ?>
            <div class="alert alert-<?php echo $edit_message_type; ?>">
                <?php echo $edit_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($profile): ?>
            <div class="profile-section">
            <div class="profile-section-header">
                <h2>📋 Jouw Gegevens</h2>
                <button type="button" class="btn-edit" onclick="openEditModal()">✏️ Bewerken</button>
                </div>
                <div class="profile-info">
                    <div class="profile-item">
                        <strong>Naam</strong>
                        <?php echo htmlspecialchars($profile['naam'] ?? 'N/A'); ?>
                    </div>
                    <div class="profile-item">
                        <strong>E-mailadres</strong>
                        <?php echo htmlspecialchars($profile['email'] ?? 'N/A'); ?>
                    </div>
                    <?php if ($role === 'supporter'): ?>
                        <div class="profile-item">
                            <strong>Adres</strong>
                            <?php echo htmlspecialchars($profile['adres'] ?? 'N/A'); ?>
                        </div>
                        <div class="profile-item">
                            <strong>Status</strong>
                            <?php 
                                $status = $_SESSION['status'] ?? 'pending';
                                if ($status === 'approved') {
                                    echo '✅ Goedgekeurd';
                                } else if ($status === 'pending') {
                                    echo '⏳ In afwachting van goedkeuring';
                                } else {
                                    echo '❌ Afgewezen';
                                }
                            ?>
                        </div>
                    <?php elseif ($role === 'referee'): ?>
                        <div class="profile-item">
                            <strong>Beschikbaarheid</strong>
                            <?php echo $profile['beschikbaarheid'] ? '✅ Beschikbaar' : '❌ Niet beschikbaar'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
            <div class="action-buttons">
                <a href="admin.php">⚙️ Admin Panel</a>
            </div>
        <?php elseif ($role === 'referee'): ?>
            <div class="action-buttons">
                <a href="referee.php">🏆 Scheidsrechter Dashboard</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <div id="editModal" class="edit-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Gegevens Bewerken</h2>
                <button class="btn-close" onclick="closeEditModal()">✕</button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="edit_naam">Naam</label>
                    <input type="text" id="edit_naam" name="naam" value="<?php echo htmlspecialchars($profile['naam'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">E-mailadres</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                </div>
                <input type="hidden" name="action" value="edit_profile">
                <button type="submit" class="btn-submit">💾 Opslaan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
