<?php

require_once 'Database.php';

class User extends Database {
    
    protected $id;
    protected $naam;
    protected $email;
    protected $wachtwoord;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getId() { return $this->id; }
    public function getNaam() { return $this->naam; }
    public function getEmail() { return $this->email; }
    public function getWachtwoord() { return $this->wachtwoord; }
    
    public function setId($id) { $this->id = $id; }
    public function setNaam($naam) { $this->naam = $naam; }
    public function setEmail($email) { $this->email = $email; }
    public function setWachtwoord($wachtwoord) { $this->wachtwoord = $wachtwoord; }
    
    public function login($email, $wachtwoord) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM beheerders WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $this->id = $user['beheerder_id'];
                $this->naam = $user['naam'];
                $this->email = $user['email'];
                $this->wachtwoord = $user['wachtwoord'];
                
                $_SESSION['user_id'] = $user['beheerder_id'];
                $_SESSION['username'] = $user['naam'];
                $_SESSION['role'] = 'admin';
                $_SESSION['email'] = $user['email'];
                return true;
            }
            
            $stmt = $this->conn->prepare("SELECT * FROM scheidsrechters WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $this->id = $user['scheidsrechter_id'];
                $this->naam = $user['naam'];
                $this->email = $user['email'];
                $this->wachtwoord = $user['wachtwoord'];
                
                $_SESSION['user_id'] = $user['scheidsrechter_id'];
                $_SESSION['username'] = $user['naam'];
                $_SESSION['role'] = 'referee';
                $_SESSION['email'] = $user['email'];
                return true;
            }
            
            $stmt = $this->conn->prepare("SELECT * FROM supporters WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $this->id = $user['supporter_id'];
                $this->naam = $user['naam'];
                $this->email = $user['email'];
                $this->wachtwoord = $user['wachtwoord'];
                
                $_SESSION['user_id'] = $user['supporter_id'];
                $_SESSION['username'] = $user['naam'];
                $_SESSION['role'] = 'supporter';
                $_SESSION['email'] = $user['email'];
                $_SESSION['status'] = $user['status'];
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return true;
    }

    public function getUser($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $role = $_SESSION['role'] ?? 'supporter';
        
        switch ($role) {
            case 'admin':
                $stmt = $this->conn->prepare("SELECT beheerder_id as user_id, naam, email FROM beheerders WHERE beheerder_id = :id");
                break;
            
            case 'referee':
                $stmt = $this->conn->prepare("SELECT scheidsrechter_id as user_id, naam, email, beschikbaarheid FROM scheidsrechters WHERE scheidsrechter_id = :id");
                break;
            
            case 'supporter':
            default:
                $stmt = $this->conn->prepare("SELECT supporter_id as user_id, naam, email, adres FROM supporters WHERE supporter_id = :id");
                break;
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function editProfile($user_id, $naam, $email, $adres = null) {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $role = $_SESSION['role'] ?? 'supporter';
            
            if ($role === 'admin') {
                $stmt = $this->conn->prepare("UPDATE beheerders SET naam = :naam, email = :email WHERE beheerder_id = :id");
                $stmt->bindParam(':id', $user_id);
                $stmt->bindParam(':naam', $naam);
                $stmt->bindParam(':email', $email);
            } elseif ($role === 'referee') {
                $stmt = $this->conn->prepare("UPDATE scheidsrechters SET naam = :naam, email = :email WHERE scheidsrechter_id = :id");
                $stmt->bindParam(':id', $user_id);
                $stmt->bindParam(':naam', $naam);
                $stmt->bindParam(':email', $email);
            } else {
                $stmt = $this->conn->prepare("UPDATE supporters SET naam = :naam, email = :email, adres = :adres WHERE supporter_id = :id");
                $stmt->bindParam(':id', $user_id);
                $stmt->bindParam(':naam', $naam);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':adres', $adres);
            }
            
            if ($stmt->execute()) {
                $this->naam = $naam;
                $this->email = $email;
                return true;
            }
            return false;
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

    public static function getSupporters() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT supporter_id, naam, email, adres FROM supporters");
        return $stmt->fetchAll();
    }
}
