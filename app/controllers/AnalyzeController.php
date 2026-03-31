<?php

class AnalyzeController extends Controller {

    public function index() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $youtubeModel = $this->model('YoutubeModel');
            $keywordModel = $this->model('KeywordModel');

            $url = $_POST['youtube_url'];
            $videoId = $youtubeModel->getVideoId($url);

            if (!$videoId) {
                header("Location: " . BASEURL . "/home");
                exit;
            }

            // ambil semua komentar
            $allComments = $youtubeModel->getComments($url);

            // ambil keyword user jika login
            $keywords = [];
            if (isset($_SESSION['user_id'])) {
                $keywords = $keywordModel->getByUser($_SESSION['user_id']);
            }

            // session-based filter font
            $applyFontFilter = !empty($_SESSION['filter_non_original_fonts']);

            // tandai komentar toxic
            $commentsWithFlag = [];
            foreach ($allComments as $c) {
                $isToxic = false;
                $matched_keyword = '';
                $category = '';

                foreach ($keywords as $k) {
                    if (stripos($c['text'], $k['word']) !== false) {
                        $isToxic = true;
                        $matched_keyword = $k['word'];
                        $category = 'Keyword';
                        break;
                    }
                }

                if (!$isToxic && $applyFontFilter) {
                    if ($this->isNonOriginalFont($c['text'])) {
                        $isToxic = true;
                        $matched_keyword = 'Non-original font';
                        $category = 'Font';
                    }
                }

                $c['is_toxic'] = $isToxic;
                $c['matched_keyword'] = $matched_keyword;
                $c['category'] = $category;

                $commentsWithFlag[] = $c;
            }

            $videoTitle = $youtubeModel->getVideoTitle($videoId);

            $_SESSION['analyze_data'] = [
                'video_id' => $videoId,
                'video_title' => $videoTitle,
                'total_comments' => count($allComments)
            ];

            $_SESSION['comments_cache'] = array_filter($commentsWithFlag, fn($c) => $c['is_toxic']);
            $_SESSION['all_comments'] = $commentsWithFlag;

            $isOwner = false;
            if (isset($_SESSION['access_token'])) {
                $isOwner = $youtubeModel->isVideoOwner($videoId, $_SESSION['access_token']);
            }

            $this->renderAnalyze(null, $isOwner);

        } else {
            header("Location: " . BASEURL . "/home");
            exit;
        }
    }

    public function delete($commentId)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASEURL . "/auth");
            exit;
        }

        if (!isset($_SESSION['access_token'])) {
            $this->renderAnalyze("Session expired, please login again.");
            return;
        }

        $youtubeModel = $this->model('YoutubeModel');
        $success = $youtubeModel->deleteComment($commentId, $_SESSION['access_token']);
        $message = "";

        if ($success) {
            if (isset($_SESSION['comments_cache'])) {
                foreach ($_SESSION['comments_cache'] as $key => $c) {
                    if ($c['id'] === $commentId) unset($_SESSION['comments_cache'][$key]);
                }
                $_SESSION['comments_cache'] = array_values($_SESSION['comments_cache']);
            }
            if (isset($_SESSION['all_comments'])) {
                foreach ($_SESSION['all_comments'] as $key => $c) {
                    if ($c['id'] === $commentId) unset($_SESSION['all_comments'][$key]);
                }
                $_SESSION['all_comments'] = array_values($_SESSION['all_comments']);
            }
            $message = "Deleted 1 comment.";
        } else {
            $message = "Failed to delete comment.";
        }

        $this->renderAnalyze($message);
    }

    public function deleteAll()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASEURL . "/auth");
            exit;
        }

        if (!isset($_SESSION['access_token'])) {
            $this->renderAnalyze("Session expired, please login again.");
            return;
        }

        if (!isset($_SESSION['comments_cache'])) {
            header("Location: " . BASEURL . "/home");
            exit;
        }

        $youtubeModel = $this->model('YoutubeModel');
        $historyModel = $this->model('HistoryModel');
        $commentModel = $this->model('CommentModel');

        $deleted = 0;
        $hidden = 0;

        // Hitung deleted/hidden
        foreach ($_SESSION['comments_cache'] as $c) {
            if (isset($c['id'])) {
                $success = $youtubeModel->deleteComment($c['id'], $_SESSION['access_token']);
                if ($success) $deleted++;
                else $hidden++;
            }
        }

        // Simpan komentar ke tabel comments sebelum session dihapus
        $now = date('Y-m-d H:i:s');
        foreach ($_SESSION['comments_cache'] as $c) {
            $status = isset($c['deleted']) && $c['deleted'] ? 'deleted' : 'hidden';
            $commentModel->create([
                'video_id' => $_SESSION['analyze_data']['video_id'],
                'author' => $c['author'] ?? '',
                'comment_text' => $c['text'],
                'category' => $c['category'] ?? '',
                'status' => $status,
                'deleted_at' => $now,
                'created_at' => $now
            ]);
        }

        // Update history
        $historyModel->create([
            'user_id' => $_SESSION['user_id'],
            'video_id' => $_SESSION['analyze_data']['video_id'],
            'video_title' => $_SESSION['analyze_data']['video_title'],
            'total_comments' => $_SESSION['analyze_data']['total_comments'],
            'deleted_comments' => $deleted,
            'hidden_comments' => $hidden
        ]);

        // Hapus session cache setelah disimpan
        $_SESSION['all_comments'] = array_filter($_SESSION['all_comments'] ?? [], fn($c) => !($c['is_toxic'] ?? false));
        $_SESSION['comments_cache'] = [];

        $message = "Deleted $deleted comments";
        if ($hidden > 0) $message .= " and Hiding $hidden comments";

        $this->renderAnalyze($message);
    }

    private function renderAnalyze($message = null, $isOwner = true)
    {
        $comments = $_SESSION['all_comments'] ?? [];

        $this->view('layouts/header');
        $this->view('analyze', [
            'comments' => $comments,
            'isOwner' => $isOwner,
            'isLoggedIn' => isset($_SESSION['user_id']),
            'message' => $message
        ]);
        $this->view('layouts/footer');
    }

    private function isNonOriginalFont($text)
    {
        return preg_match('/[^\x00-\x7F]/', $text);
    }
}