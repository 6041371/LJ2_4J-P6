<?php

require_once 'User.php';

class Supporter extends User {
    
    public function register($naam, $wachtwoord, $email, $adres) {
        try {
            $hashed_password = password_hash($wachtwoord, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO supporters (naam, wachtwoord, email, adres, status) 
                                         VALUES (:naam, :wachtwoord, :email, :adres, 'pending')");
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':wachtwoord', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':adres', $adres);
            $stmt->execute();
            
            $this->naam = $naam;
            $this->email = $email;
            $this->wachtwoord = $hashed_password;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT supporter_id FROM supporters WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getSupporter($id) {
        $stmt = $this->conn->prepare("SELECT supporter_id, naam, email, adres, status, fan_id FROM supporters WHERE supporter_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function ReceiveFanId($supporter_id) {
        try {
            $stmt = $this->conn->prepare("SELECT fan_id FROM supporters WHERE supporter_id = :id");
            $stmt->bindParam(':id', $supporter_id);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? $result['fan_id'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
}

?>
