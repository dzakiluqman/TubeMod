<?php

class HistoryModel {

    private $conn;

    public function __construct()
    {
        require_once '../app/config/database.php';
        $this->conn = getConnection();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO history 
            (user_id, video_id, video_title, total_comments, deleted_comments) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issii",
            $data['user_id'],
            $data['video_id'],
            $data['video_title'],
            $data['total_comments'],
            $data['deleted_comments']
        );

        return $stmt->execute();
    }

    public function getByUser($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM history WHERE user_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}