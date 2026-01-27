<?php
// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check of beheerder ingelogd is
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'classes/Beheerder.php';

$beheerder = new Beheerder();
$message = '';
$message_type = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $naam = $_POST['naam'] ?? '';
        $email = $_POST['email'] ?? '';
        $beschikbaarheid = $_POST['beschikbaarheid'] ?? 1;

        if (empty($naam) || empty($email)) {
            $message = "❌ Naam en e-mail zijn verplicht";
            $message_type = 'error';
        } else {
            $result = $beheerder->createScheidsrechter($naam, $email, $beschikbaarheid);
            if ($result) {
                $message = "✅ Scheidsrechter toegevoegd!<br><strong>📧 Email:</strong> {$email}<br><strong>🔐 Wachtwoord:</strong> <strong>{$result['password']}</strong>";
                $message_type = 'success';
            } else {
                $message = "❌ Fout bij toevoegen scheidsrechter";
                $message_type = 'error';
            }
        }
    } elseif ($action === 'update') {
        $scheidsrechter_id = $_POST['scheidsrechter_id'] ?? null;
        $naam = $_POST['naam'] ?? '';
        $email = $_POST['email'] ?? '';
        $beschikbaarheid = $_POST['beschikbaarheid'] ?? 1;

        if ($beheerder->updateScheidsrechter($scheidsrechter_id, $naam, $email, $beschikbaarheid)) {
            $message = "✅ Scheidsrechter bijgewerkt";
            $message_type = 'success';
        } else {
            $message = "❌ Fout bij bijwerken";
            $message_type = 'error';
        }
    } elseif ($action === 'delete') {
        $scheidsrechter_id = $_POST['scheidsrechter_id'] ?? null;
        
        if ($beheerder->isRefereeBookedForFutureMatch($scheidsrechter_id)) {
            $message = "❌ Deze scheidsrechter is gekoppeld aan een toekomstige wedstrijd en kan niet worden verwijderd";
            $message_type = 'error';
        } else {
            if ($beheerder->deleteScheidsrechter($scheidsrechter_id)) {
                $message = "✅ Scheidsrechter verwijderd";
                $message_type = 'success';
            } else {
                $message = "❌ Fout bij verwijderen";
                $message_type = 'error';
            }
        }
    }
}

// Get alle scheidsrechters
$referees = $beheerder->getAllReferees();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Scheidsrechters Beheren - Afrika Cup</title>
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <div class="manage-container">
        <div class="page-header">
            <div>
                <h1>👨‍⚖️ Scheidsrechters Beheren</h1>
                <p>Voeg, wijzig of verwijder scheidsrechters</p>
            </div>
            <a href="admin_panel.php" class="btn-back">← Terug naar Admin</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Toevoegen Form -->
        <div class="form-section">
            <h2>➕ Nieuwe Scheidsrechter Toevoegen</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="naam">Naam *</label>
                        <input type="text" id="naam" name="naam" placeholder="Bijv. Jan de Vries" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mailadres *</label>
                        <input type="email" id="email" name="email" placeholder="bijv. jan@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="beschikbaarheid">Beschikbaarheid</label>
                        <select id="beschikbaarheid" name="beschikbaarheid">
                            <option value="1">✅ Beschikbaar</option>
                            <option value="0">❌ Niet beschikbaar</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn-submit">➕ Toevoegen</button>
            </form>
        </div>

        <!-- Scheidsrechters Lijst -->
        <div class="referees-list">
            <h2>📋 Alle Scheidsrechters (<?php echo count($referees); ?>)</h2>
            
            <?php if (count($referees) > 0): ?>
                <?php foreach ($referees as $referee): ?>
                    <div class="referee-item">
                        <div class="referee-info">
                            <h3><?php echo htmlspecialchars($referee['naam']); ?></h3>
                            <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($referee['email']); ?></p>
                            <p><strong>Status:</strong> 
                                <?php echo $referee['beschikbaarheid'] ? '✅ Beschikbaar' : '❌ Niet beschikbaar'; ?>
                            </p>
                        </div>
                        <div class="referee-actions">
                            <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($referee)); ?>)">✏️ Bewerken</button>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="scheidsrechter_id" value="<?php echo $referee['scheidsrechter_id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Weet je zeker dat je deze scheidsrechter wilt verwijderen?');">🗑️ Verwijderen</button>
                            </form>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-referees">
                    <p>Geen scheidsrechters gevonden. Voeg een nieuwe toe!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="edit-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Scheidsrechter Bewerken</h2>
                <button class="btn-close" onclick="closeEditModal()">✕</button>
            </div>
            <form method="POST" id="editForm">
                <div class="form-group">
                    <label for="edit_naam">Naam</label>
                    <input type="text" id="edit_naam" name="naam" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">E-mailadres</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit_beschikbaarheid">Beschikbaarheid</label>
                    <select id="edit_beschikbaarheid" name="beschikbaarheid">
                        <option value="1">✅ Beschikbaar</option>
                        <option value="0">❌ Niet beschikbaar</option>
                    </select>
                </div>
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_id" name="scheidsrechter_id">
                <button type="submit" class="btn-submit">💾 Opslaan</button>
            </form>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>

    <script>
        function openEditModal(referee) {
            document.getElementById('edit_naam').value = referee.naam;
            document.getElementById('edit_email').value = referee.email;
            document.getElementById('edit_beschikbaarheid').value = referee.beschikbaarheid;
            document.getElementById('edit_id').value = referee.scheidsrechter_id;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Sluit modal als buiten wordt geklikt
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
