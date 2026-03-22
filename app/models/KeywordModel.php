<?php

class KeywordModel {

    private $conn;

    public function __construct()
    {
        require_once '../app/config/database.php';
        $this->conn = getConnection();
    }

    // READ (by user)
    public function getAllByUser($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM keywords WHERE user_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function getByUser($user_id) {

        $stmt = $this->conn->prepare("SELECT * FROM keywords WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // CREATE
    public function add($user_id, $word, $category)
    {
        $stmt = $this->conn->prepare("INSERT INTO keywords (user_id, word, category) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $word, $category);
        return $stmt->execute();
    }

    // UPDATE
    public function update($id, $user_id, $word, $category)
    {
        $stmt = $this->conn->prepare("UPDATE keywords SET word=?, category=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssii", $word, $category, $id, $user_id);
        return $stmt->execute();
    }

    // DELETE
    public function delete($id, $user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM keywords WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }
}