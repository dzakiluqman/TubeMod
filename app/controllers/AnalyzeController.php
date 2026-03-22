<?php

class AnalyzeController extends Controller {

    public function index() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $youtubeModel = $this->model('YoutubeModel');
            $keywordModel = $this->model('KeywordModel');
            $filterModel = $this->model('FilterModel');

            $url = $_POST['youtube_url'];

            // ambil komentar
            $comments = $youtubeModel->getComments($url);

            // ambil keyword user
            $keywords = $keywordModel->getByUser($_SESSION['user_id']);

            // filter komentar
            $comments = $filterModel->filterComments($comments, $keywords);

            // simpan untuk deleteAll
            $_SESSION['comments_cache'] = $comments;

            // cek ownership
            $videoId = $youtubeModel->getVideoId($url);

            $isOwner = false;
            if (isset($_SESSION['access_token'])) {
                $isOwner = $youtubeModel->isVideoOwner($videoId, $_SESSION['access_token']);
            }

            $this->view('layouts/header');
            $this->view('analyze', [
                'comments' => $comments,
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

        foreach ($_SESSION['comments_cache'] as $c) {
            if (isset($c['id'])) {
                $youtubeModel->deleteComment(
                    $c['id'],
                    $_SESSION['access_token']
                );
            }
        }

        // clear cache setelah delete
        unset($_SESSION['comments_cache']);

        header("Location: " . BASEURL . "/home");
        exit;
    }
}