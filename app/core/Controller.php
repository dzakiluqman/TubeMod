<?php

// Wajib dipanggil agar class JWT bisa digunakan di fungsi verifyToken
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller {

    public function view($view, $data = []) {
        extract($data);
        require_once 'app/views/' . $view . '.php';
    }

    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model;
    }

    // --- API HELPER METHODS --- //

    public function sendJsonSuccess($data = [], $message = "Success", $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function sendJsonError($message = "Error", $statusCode = 400) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode
        ]);
        exit;
    }

    // --- API SECURITY MIDDLEWARE (SATPAM JWT) --- //

    /**
     * Memverifikasi JWT Token dari header Authorization
     * Mengembalikan data payload jika valid, memblokir request jika tidak valid.
     */
    public function verifyToken() {
        // 1. Ambil semua header request
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Untuk server Nginx / FastCGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        // 2. Jika header Authorization sama sekali tidak dikirim oleh Flutter
        if (empty($headers)) {
            $this->sendJsonError('Akses ditolak. Token tidak ditemukan pada Header.', 401);
        }

        // 3. Ekstrak token dari format "Bearer <token_jwt>"
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            $token = $matches[1];
        } else {
            $this->sendJsonError('Format token tidak valid. Harus menggunakan format Bearer.', 401);
        }

        // 4. Verifikasi keaslian Token
        try {
            // Mengambil secret key (Mendukung pembacaan dari .env atau konstanta define)
            $secretKey = isset($_ENV['JWT_SECRET']) ? $_ENV['JWT_SECRET'] : JWT_SECRET;
            
            // Proses decode dan validasi expired time otomatis
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            
            // Kembalikan isi payload token (contoh: user_id) untuk dipakai di Controller API
            return $decoded; 
            
        } catch (Exception $e) {
            // Jika token palsu, diubah hacker, atau masa berlakunya sudah habis
            $this->sendJsonError('Token tidak valid atau kedaluwarsa: ' . $e->getMessage(), 401);
        }
    }
}