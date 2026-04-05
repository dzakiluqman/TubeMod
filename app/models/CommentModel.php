<?php

class CommentModel {

    private $conn;

    public function __construct()
    {
        require_once 'app/config/database.php';
        $this->conn = getConnection();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO comments
            (video_id, author, comment_text, category, status, deleted_at, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssss",
            $data['video_id'],
            $data['author'],
            $data['comment_text'],
            $data['category'],
            $data['status'],
            $data['deleted_at'],
            $data['created_at']
        );

        return $stmt->execute();
    }

    public function getByVideo($video_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM comments
            WHERE video_id = ?
            ORDER BY deleted_at DESC
        ");
        $stmt->bind_param("s", $video_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}