<?php

class HistoryController extends Controller {

    private $historyModel;
    private $userData;

    public function __construct() {
        // 1. Verifikasi token JWT
        $decodedToken = $this->verifyToken();
        $this->userData = $decodedToken->data;

        // 2. Load model
        $this->historyModel = $this->model('HistoryModel');
    }

    /**
     * GET /api/history
     * Mengambil daftar riwayat moderasi milik user yang sedang login
     */
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        // Ambil riwayat berdasarkan user_id dari JWT
        $historyData = $this->historyModel->getByUser($this->userData->user_id);

        if (empty($historyData)) {
            // Kembalikan array kosong jika belum ada riwayat
            $this->sendJsonSuccess([], 'Belum ada riwayat moderasi.');
        } else {
            $this->sendJsonSuccess($historyData, 'Riwayat moderasi berhasil diambil.');
        }
    }

    /**
     * DELETE /api/history/delete/5
     * Menghapus satu riwayat moderasi
     */
    public function delete($id = null) {
        // Mendukung DELETE atau POST (fallback)
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        if (empty($id)) {
            $this->sendJsonError('ID riwayat wajib disertakan.', 400);
        }

        // Hapus riwayat (pastikan dicocokkan dengan user_id agar aman)
        $success = $this->historyModel->deleteById($id, $this->userData->user_id);

        if ($success) {
            $this->sendJsonSuccess(null, 'Riwayat berhasil dihapus.');
        } else {
            $this->sendJsonError('Gagal menghapus riwayat atau riwayat tidak ditemukan.', 404);
        }
    }
}