<?php

class HistoryController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login?error=session_expired');
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

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASEURL . "/auth/login");
            exit;
        }

        $historyModel = $this->model('HistoryModel');

        $success = $historyModel->deleteById($id, $_SESSION['user_id']);

        if ($success) {
            header("Location: " . BASEURL . "/history?message=Deleted successfully");
        } else {
            header("Location: " . BASEURL . "/history?message=Failed to delete");
        }
    }
}