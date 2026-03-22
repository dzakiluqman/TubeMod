<?php

class AnalyzeController extends Controller {

    public function index() {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $youtubeModel = $this->model('YoutubeModel');
            $keywordModel = $this->model('KeywordModel');
            $filterModel = $this->model('FilterModel');

            $url = $_POST['youtube_url'];

            // 🔥 ambil videoId dulu
            $videoId = $youtubeModel->getVideoId($url);

            if (!$videoId) {
                header("Location: " . BASEURL . "/home");
                exit;
            }

            // ambil komentar asli
            $allComments = $youtubeModel->getComments($url);

            // ambil keyword user
            $keywords = $keywordModel->getByUser($_SESSION['user_id']);

            // filter komentar (toxic only)
            $filteredComments = $filterModel->filterComments($allComments, $keywords);

            // ambil title video
            $videoTitle = $youtubeModel->getVideoTitle($videoId);

            // simpan data untuk history
            $_SESSION['analyze_data'] = [
                'video_id' => $videoId,
                'video_title' => $videoTitle,
                'total_comments' => count($allComments)
            ];

            // simpan komentar toxic untuk deleteAll
            $_SESSION['comments_cache'] = $filteredComments;

            // cek ownership
            $isOwner = false;
            if (isset($_SESSION['access_token'])) {
                $isOwner = $youtubeModel->isVideoOwner($videoId, $_SESSION['access_token']);
            }

            $this->view('layouts/header');
            $this->view('analyze', [
                'comments' => $filteredComments,
                'isOwner' => $isOwner
            ]);
            $this->view('layouts/footer');

        } else {
            header("Location: " . BASEURL . "/home");
            exit;
        }
    }

    public function delete($commentId)
    {
        if (!isset($_SESSION['access_token'])) {
            header("Location: " . BASEURL . "/home");
            exit;
        }

        $youtubeModel = $this->model('YoutubeModel');

        $success = $youtubeModel->deleteComment(
            $commentId,
            $_SESSION['access_token']
        );

        // balik ke halaman sebelumnya
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function deleteAll()
    {
        if (!isset($_SESSION['access_token'])) {
            header("Location: " . BASEURL . "/home");
            exit;
        }

        if (!isset($_SESSION['comments_cache'])) {
            header("Location: " . BASEURL . "/home");
            exit;
        }

        $youtubeModel = $this->model('YoutubeModel');
        $historyModel = $this->model('HistoryModel');

        $deleted = 0;

        foreach ($_SESSION['comments_cache'] as $c) {
            if (isset($c['id'])) {
                $youtubeModel->deleteComment(
                    $c['id'],
                    $_SESSION['access_token']
                );
            }
        }

        // simpan history
        $historyModel->create([
            'user_id' => $_SESSION['user_id'],
            'video_id' => $_SESSION['analyze_data']['video_id'],
            'video_title' => $_SESSION['analyze_data']['video_title'],
            'total_comments' => $_SESSION['analyze_data']['total_comments'],
            'deleted_comments' => $deleted
        ]);

        // clear cache setelah delete
        unset($_SESSION['comments_cache']);

        header("Location: " . BASEURL . "/home");
        exit;
    }
}