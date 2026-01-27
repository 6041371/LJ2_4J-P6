<?php

require_once 'User.php';

class Beheerder extends User {
    
    // Registreer nieuwe BEHEERDER
    public function register($naam, $wachtwoord, $email, $adres = null) {
        try {
            // Wachtwoord hashen
            $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO beheerders (naam, wachtwoord, email) 
                                         VALUES (:naam, :wachtwoord, :email)");
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':wachtwoord', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check of email al bestaat in beheerders
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT beheerder_id FROM beheerders WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Get beheerder info
    public function getAdmin($id) {
        $stmt = $this->conn->prepare("SELECT beheerder_id, naam, email FROM beheerders WHERE beheerder_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get alle beheerders
    public static function getAllAdmins() {
        $user = new User();
        $conn = $user->getConnection();
        $stmt = $conn->query("SELECT beheerder_id, naam, email FROM beheerders");
        return $stmt->fetchAll() ?: [];
    }

    // Delete beheerder
    public function deleteAdmin($id) {
        $stmt = $this->conn->prepare("DELETE FROM beheerders WHERE beheerder_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // ==========================
    // SUPPORTER APPROVAL METHODS
    // ==========================

    // Get alle pending supporters voor goedkeuring
    public function getPendingSupporters() {
        try {
            $stmt = $this->conn->prepare("SELECT supporter_id, naam, email, adres, status FROM supporters WHERE status = 'pending' ORDER BY supporter_id DESC");
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    // Goedkeuren supporter
    public function approveSupporter($supporter_id) {
        try {
            $stmt = $this->conn->prepare("UPDATE supporters SET status = 'approved' WHERE supporter_id = :supporter_id");
            $stmt->bindParam(':supporter_id', $supporter_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Toekennen Fan-ID
    public function assignFanId($supporter_id) {
        try {
            // Genereer Fan-ID (format: FAN-XXXXXX)
            $fan_id = 'FAN-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            $stmt = $this->conn->prepare("UPDATE supporters SET fan_id = :fan_id WHERE supporter_id = :supporter_id");
            $stmt->bindParam(':supporter_id', $supporter_id);
            $stmt->bindParam(':fan_id', $fan_id);
            $stmt->execute();
            
            return $fan_id;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Afwijzen supporter
    public function rejectSupporter($supporter_id) {
        try {
            $stmt = $this->conn->prepare("UPDATE supporters SET status = 'rejected' WHERE supporter_id = :supporter_id");
            $stmt->bindParam(':supporter_id', $supporter_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================
    // SCHEIDSRECHTER MANAGEMENT
    // ==========================

    // Voeg nieuwe scheidsrechter handmatig toe
    public function createScheidsrechter($naam, $email, $beschikbaarheid = 1) {
        try {
            // Genereer automatisch wachtwoord
            $wachtwoord = bin2hex(random_bytes(8));
            $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO scheidsrechters (naam, email, wachtwoord, beschikbaarheid) 
                                         VALUES (:naam, :email, :wachtwoord, :beschikbaarheid)");
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':wachtwoord', $hashed_password);
            $stmt->bindParam(':beschikbaarheid', $beschikbaarheid);
            $stmt->execute();
            
            return ['id' => $this->conn->lastInsertId(), 'password' => $wachtwoord];
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get alle scheidsrechters
    public function getAllReferees() {
        try {
            $stmt = $this->conn->query("SELECT scheidsrechter_id, naam, email, beschikbaarheid FROM scheidsrechters ORDER BY naam");
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get scheidsrechter details
    public function getRefereeDetails($scheidsrechter_id) {
        try {
            $stmt = $this->conn->prepare("SELECT scheidsrechter_id, naam, email, beschikbaarheid FROM scheidsrechters WHERE scheidsrechter_id = :id");
            $stmt->bindParam(':id', $scheidsrechter_id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Update scheidsrechter gegevens
    public function updateScheidsrechter($scheidsrechter_id, $naam, $email, $beschikbaarheid) {
        try {
            $stmt = $this->conn->prepare("UPDATE scheidsrechters SET naam = :naam, email = :email, beschikbaarheid = :beschikbaarheid WHERE scheidsrechter_id = :id");
            $stmt->bindParam(':id', $scheidsrechter_id);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':beschikbaarheid', $beschikbaarheid);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Check of scheidsrechter is gekoppeld aan toekomstige wedstrijd
    public function isRefereeBookedForFutureMatch($scheidsrechter_id) {
        try {
            $today = date('Y-m-d');
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count FROM wedstrijden 
                WHERE scheidsrechter_id = :id AND datum > :datum
            ");
            $stmt->bindParam(':id', $scheidsrechter_id);
            $stmt->bindParam(':datum', $today);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return true; // Veilig: als error, niet verwijderen
        }
    }

    // Delete scheidsrechter (alleen als niet gekoppeld aan toekomstige wedstrijd)
    public function deleteScheidsrechter($scheidsrechter_id) {
        try {
            if ($this->isRefereeBookedForFutureMatch($scheidsrechter_id)) {
                return false; // Gekoppeld aan toekomstige wedstrijd
            }
            
            $stmt = $this->conn->prepare("DELETE FROM scheidsrechters WHERE scheidsrechter_id = :id");
            $stmt->bindParam(':id', $scheidsrechter_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
