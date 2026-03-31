<?php

class HistoryController extends Controller {

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASEURL . "/auth/login");
            exit;
        }
    }

    public function index()
    {
        $historyModel = $this->model('HistoryModel');
        $data['history'] = $historyModel->getByUser($_SESSION['user_id']);

        $this->view('history', $data);
        $this->view('layouts/footer');
    }

    // Method untuk AJAX modal
    public function detail($video_id)
    {
        $historyModel = $this->model('HistoryModel');
        $commentModel = $this->model('CommentModel');

        // Ambil video history untuk user ini
        $video = $historyModel->getByVideoIdAndUser($video_id, $_SESSION['user_id']);

        if (!$video) {
            echo json_encode(['error' => 'Video not found']);
            exit;
        }

        // Ambil komentar deleted/hidden untuk video ini
        $comments = $commentModel->getByVideo($video_id);

        echo json_encode([
            'video' => $video,
            'comments' => $comments
        ]);
    }
}