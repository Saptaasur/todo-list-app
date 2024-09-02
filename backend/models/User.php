<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $reset_token;
    public $reset_token_expires;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, email=:email, password=:password";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        return $stmt->execute();
    }

    public function login() {
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            return true;
        }
        return false;
    }

    public function generateResetToken() {
        $this->reset_token = bin2hex(random_bytes(16));
        $this->reset_token_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE " . $this->table_name . " SET reset_token = :reset_token, reset_token_expires = :reset_token_expires WHERE email = :email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":reset_token", $this->reset_token);
        $stmt->bindParam(":reset_token_expires", $this->reset_token_expires);
        $stmt->bindParam(":email", $this->email);

        return $stmt->execute();
    }

    public function resetPassword($new_password) {
        $this->password = password_hash($new_password, PASSWORD_BCRYPT);

        $query = "UPDATE " . $this->table_name . " SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = :reset_token AND reset_token_expires > NOW()";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":reset_token", $this->reset_token);

        return $stmt->execute();
    }
}
?>
