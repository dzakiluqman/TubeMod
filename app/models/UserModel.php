<?php

class UserModel {

    private $conn;

    public function __construct()
    {
        require_once 'app/config/database.php';
        $this->conn = getConnection();
    }

    public function getByGoogleId($google_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO users (google_id, name, email, picture)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $data['google_id'],
            $data['name'],
            $data['email'],
            $data['picture']
        );

        return $stmt->execute();
    }
}