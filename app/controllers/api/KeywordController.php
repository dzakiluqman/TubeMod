<?php

class KeywordController extends Controller {

    private $model;
    private $userData;

    public function __construct() {
        // 1. Verifikasi token setiap kali API ini dipanggil
        $decodedToken = $this->verifyToken();
        
        // 2. Simpan data user (dari payload JWT) untuk digunakan di fungsi lain
        $this->userData = $decodedToken->data; 

        // 3. Load model
        $this->model = $this->model('KeywordModel');
    }

    // GET /api/keyword
    public function index() {
        // Menggunakan getByUser agar return berupa Array of Object
        $keywords = $this->model->getByUser($this->userData->user_id);
        
        $this->sendJsonSuccess($keywords, 'Data keyword berhasil diambil');
    }

    // POST /api/keyword/add
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        // Ambil payload JSON dari Flutter
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['word']) || empty($input['category'])) {
            $this->sendJsonError('Data tidak lengkap. Field word dan category wajib diisi.', 400);
        }

        $success = $this->model->add($this->userData->user_id, $input['word'], $input['category']);

        if ($success) {
            $this->sendJsonSuccess(null, 'Keyword berhasil ditambahkan', 201);
        } else {
            $this->sendJsonError('Gagal menambahkan keyword', 500);
        }
    }

    // PUT atau POST /api/keyword/update
    public function update() {
        // Mendukung PUT atau POST untuk fleksibilitas di Flutter
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id']) || empty($input['word']) || empty($input['category'])) {
            $this->sendJsonError('Data tidak lengkap. Field id, word, dan category wajib diisi.', 400);
        }

        $success = $this->model->update($input['id'], $this->userData->user_id, $input['word'], $input['category']);

        if ($success) {
            $this->sendJsonSuccess(null, 'Keyword berhasil diperbarui');
        } else {
            $this->sendJsonError('Gagal memperbarui keyword atau keyword tidak ditemukan', 500);
        }
    }

    // DELETE /api/keyword/delete/5
    public function delete($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            // Opsi fallback jika HTTP DELETE terblokir di server
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
                $this->sendJsonError('Method Not Allowed', 405);
            }
        }

        if (empty($id)) {
            $this->sendJsonError('ID keyword wajib disertakan', 400);
        }

        $success = $this->model->delete($id, $this->userData->user_id);

        if ($success) {
            $this->sendJsonSuccess(null, 'Keyword berhasil dihapus');
        } else {
            $this->sendJsonError('Gagal menghapus keyword', 500);
        }
    }

    // TENTANG SAVE FILTERS (Catatan Integrasi Mobile)
    public function save_filters() {
        // Di aplikasi mobile, status toggle switch (filter on/off) sebaiknya disimpan 
        // secara lokal di perangkat menggunakan SharedPreferences (Flutter). 
        // Jika Anda ingin menyimpannya di server agar sinkron antar HP, 
        // Anda harus membuat tabel/kolom 'settings' di database, bukan memakai $_SESSION.
        
        $this->sendJsonError('Endpoint ini tidak didukung di versi mobile. Simpan preferensi filter menggunakan SharedPreferences di aplikasi Flutter Anda.', 400);
    }
}