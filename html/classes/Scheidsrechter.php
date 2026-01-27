<?php

require_once 'User.php';

class Scheidsrechter extends User {
    
    public function register($naam, $wachtwoord, $email, $adres = null) {
        try {
            $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO scheidsrechters (naam, wachtwoord, email, beschikbaarheid) 
                                         VALUES (:naam, :wachtwoord, :email, 1)");
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':wachtwoord', $hashed_password);
            $stmt->bindParam(':email', $email);
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

    public function setAvailability($scheidsrechter_id, $beschikbaarheid) {
        try {
            $stmt = $this->conn->prepare("UPDATE scheidsrechters SET beschikbaarheid = :beschikbaarheid WHERE scheidsrechter_id = :id");
            $stmt->bindParam(':id', $scheidsrechter_id);
            $stmt->bindParam(':beschikbaarheid', $beschikbaarheid);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteReferee($scheidsrechter_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM scheidsrechters WHERE scheidsrechter_id = :id");
            $stmt->bindParam(':id', $scheidsrechter_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>
