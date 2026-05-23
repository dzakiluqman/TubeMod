<?php

class AnalyzeController extends Controller {

    private $youtubeModel;
    private $keywordModel;
    private $historyModel;
    private $commentModel;
    private $userData;

    public function __construct() {
        // 1. Amankan endpoint dengan "Satpam JWT" kita
        $decodedToken = $this->verifyToken();
        $this->userData = $decodedToken->data; // Dapatkan user_id dari JWT

        // 2. Inisialisasi semua model yang dibutuhkan
        $this->youtubeModel = $this->model('YoutubeModel');
        $this->keywordModel = $this->model('KeywordModel');
        $this->historyModel = $this->model('HistoryModel');
        $this->commentModel = $this->model('CommentModel');
    }

    /**
     * POST /api/analyze
     * Mengambil dan menyaring komentar berdasarkan kata kunci user
     */
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        // Ambil input JSON dari Flutter
        $input = json_decode(file_get_contents('php://input'), true);
        $url = $input['youtube_url'] ?? null;
        $googleAccessToken = $input['google_access_token'] ?? null; // Dikirim dari Google Sign-In Flutter
        $applyFontFilter = !empty($input['filter_fonts']); // Boolean true/false dari Flutter

        if (!$url) {
            $this->sendJsonError('URL YouTube wajib diisi.', 400);
        }

        $videoId = $this->youtubeModel->getVideoId($url);
        if (!$videoId) {
            $this->sendJsonError('Format URL YouTube tidak valid.', 400);
        }

        // 1. Ambil semua komentar dari YouTube API
        $allComments = $this->youtubeModel->getComments($url);
        if (empty($allComments)) {
            $this->sendJsonError('Gagal mengambil komentar atau komentar dinonaktifkan.', 404);
        }

        // 2. Ambil keyword milik user yang sedang login
        $keywords = $this->keywordModel->getByUser($this->userData->user_id);

        // 3. Proses penyaringan/flagging komentar toxic
        $commentsWithFlag = [];
        foreach ($allComments as $c) {
            $isToxic = false;
            $matchedKeyword = '';
            $category = '';

            // Cek kecocokan kata kunci
            foreach ($keywords as $k) {
                if (stripos($c['text'], $k['word']) !== false) {
                    $isToxic = true;
                    $matchedKeyword = $k['word'];
                    $category = 'Keyword';
                    break;
                }
            }

            // Cek kecocokan font tiruan (jika diaktifkan oleh Flutter)
            if (!$isToxic && $applyFontFilter) {
                if ($this->isNonOriginalFont($c['text'])) {
                    $isToxic = true;
                    $matchedKeyword = 'Non-original font';
                    $category = 'Font';
                }
            }

            $c['is_toxic'] = $isToxic;
            $c['matched_keyword'] = $matchedKeyword;
            $c['category'] = $category;

            $commentsWithFlag[] = $c;
        }

        $videoTitle = $this->youtubeModel->getVideoTitle($videoId);

        // 4. Cek kepemilikan video jika Flutter mengirimkan google_access_token
        $isOwner = false;
        if ($googleAccessToken) {
            $isOwner = $this->youtubeModel->isVideoOwner($videoId, $googleAccessToken);
        }

        // 5. Kembalikan data utuh ke Flutter untuk ditampilkan di UI mobile
        $responseData = [
            'video_id' => $videoId,
            'video_title' => $videoTitle,
            'total_comments' => count($allComments),
            'is_owner' => $isOwner,
            'comments' => $commentsWithFlag
        ];

        $this->sendJsonSuccess($responseData, 'Analisis komentar berhasil diselesaikan.');
    }

    /**
     * POST /api/analyze/deleteSingle
     * Menghapus atau menyembunyikan SATU komentar saja
     */
    public function deleteSingle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;
        $googleAccessToken = $input['google_access_token'] ?? null;

        if (!$commentId || !$googleAccessToken) {
            $this->sendJsonError('comment_id dan google_access_token wajib disertakan.', 400);
        }

        $success = $this->youtubeModel->deleteComment($commentId, $googleAccessToken);

        if ($success) {
            $this->sendJsonSuccess(null, 'Komentar berhasil dimoderasi (Dihapus/Disembunyikan).');
        } else {
            $this->sendJsonError('Gagal memoderasi komentar. Pastikan token Google valid dan Anda adalah pemilik channel.', 500);
        }
    }

    /**
     * POST /api/analyze/deleteAll
     * Menghapus massal komentar toxic dan mencatatnya ke database
     */
    public function deleteAll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $googleAccessToken = $input['google_access_token'] ?? null;
        $videoId = $input['video_id'] ?? null;
        $videoTitle = $input['video_title'] ?? null;
        $totalComments = $input['total_comments'] ?? 0;
        
        // Flutter harus mengirimkan daftar komentar toxic hasil filter sebelumnya
        $toxicComments = $input['toxic_comments'] ?? []; 

        if (!$googleAccessToken || !$videoId || empty($toxicComments)) {
            $this->sendJsonError('Data tidak lengkap. google_access_token, video_id, dan daftar toxic_comments wajib diisi.', 400);
        }

        $deleted = 0;
        $hidden = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($toxicComments as $c) {
            if (isset($c['id'])) {
                // Eksekusi ke YouTube API
                $success = $this->youtubeModel->deleteComment($c['id'], $googleAccessToken);
                
                if ($success) {
                    $deleted++;
                    $status = 'deleted';
                } else {
                    $hidden++;
                    $status = 'hidden';
                }

                // Simpan rincian komentar yang dimoderasi ke tabel `comments`
                $this->commentModel->create([
                    'video_id' => $videoId,
                    'author' => $c['author'] ?? 'Unknown',
                    'comment_text' => $c['text'],
                    'category' => $c['category'] ?? 'Keyword',
                    'status' => $status,
                    'deleted_at' => $now,
                    'created_at' => $now
                ]);
            }
        }

        // Catat ringkasan aksi ke tabel `history`
        $this->historyModel->create([
            'user_id' => $this->userData->user_id,
            'video_id' => $videoId,
            'video_title' => $videoTitle ?? 'YouTube Video',
            'total_comments' => $totalComments,
            'deleted_comments' => $deleted,
            'hidden_comments' => $hidden
        ]);

        $this->sendJsonSuccess([
            'deleted_count' => $deleted,
            'hidden_count' => $hidden
        ], "Pembersihan selesai! Berhasil menghapus $deleted komentar dan menyembunyikan $hidden komentar.");
    }

    private function isNonOriginalFont($text) {
        return preg_match('/[^\x00-\x7F]/', $text);
    }
}