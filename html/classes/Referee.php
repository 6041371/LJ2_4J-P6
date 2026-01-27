<?php

require_once 'User.php';

class Referee extends User {
    
    public function register($naam, $wachtwoord, $email, $adres = null) {
        try {
            $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO scheidsrechters (naam, wachtwoord, email, beschikbaarheid) 
                                         VALUES (:naam, :wachtwoord, :email, :beschikbaarheid)");
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':wachtwoord', $hashed_password);
            $stmt->bindParam(':email', $email);
            $beschikbaarheid = 1;
            $stmt->bindParam(':beschikbaarheid', $beschikbaarheid);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT scheidsrechter_id FROM scheidsrechters WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getReferee($id) {
        $stmt = $this->conn->prepare("SELECT scheidsrechter_id, naam, email, beschikbaarheid FROM scheidsrechters WHERE scheidsrechter_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getAllReferees() {
        $user = new User();
        $conn = $user->getConnection();
        $stmt = $conn->query("SELECT scheidsrechter_id, naam, email, beschikbaarheid FROM scheidsrechters");
        return $stmt->fetchAll() ?: [];
    }

    public function setAvailability($id, $beschikbaarheid) {
        $stmt = $this->conn->prepare("UPDATE scheidsrechters SET beschikbaarheid = :beschikbaarheid WHERE scheidsrechter_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':beschikbaarheid', $beschikbaarheid);
        return $stmt->execute();
    }

    public function deleteReferee($id) {
        $stmt = $this->conn->prepare("DELETE FROM scheidsrechters WHERE scheidsrechter_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
