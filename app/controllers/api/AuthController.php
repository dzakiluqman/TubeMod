<?php

use Firebase\JWT\JWT;

// Memastikan autoload composer dimuat (sesuaikan path-nya jika perlu)
require_once 'vendor/autoload.php';

class AuthController extends Controller {
    
    // PENTING: Pindahkan secret key ini ke config.php agar lebih aman nantinya
    private $jwt_secret = 'YOUR_SUPER_SECRET_KEY_TUBE_MOD_2024'; 

    public function login() {
        // Hanya izinkan metode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonError('Method Not Allowed', 405);
        }

        // Ambil data JSON yang dikirim oleh Flutter
        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi payload
        if (!isset($input['google_id']) || !isset($input['email'])) {
            $this->sendJsonError('Data tidak lengkap. Dibutuhkan google_id dan email.', 400);
        }

        $userModel = $this->model('UserModel');
        $existingUser = $userModel->getByGoogleId($input['google_id']);

        // Jika user belum ada, daftarkan
        if (!$existingUser) {
            $userModel->create([
                'google_id' => $input['google_id'],
                'name'      => $input['name'] ?? 'Unknown User',
                'email'     => $input['email'],
                'picture'   => $input['picture'] ?? ''
            ]);
            $existingUser = $userModel->getByGoogleId($input['google_id']);
        }

        // Payload data untuk JWT
        $payload = [
            'iss' => 'tubemod_api', // Issuer
            'aud' => 'tubemod_flutter_app', // Audience
            'iat' => time(), // Waktu token dibuat (Issued At)
            'exp' => time() + (60 * 60 * 24 * 30), // Token kedaluwarsa 30 hari
            'data' => [
                'user_id'   => $existingUser['id'],
                'google_id' => $existingUser['google_id']
                // Jika Flutter juga mengirimkan youtube_access_token, bisa disimpan di sini atau di DB
            ]
        ];

        // Generate Token
        $jwt = JWT::encode($payload, $this->jwt_secret, 'HS256');

        // Kembalikan respons standar ke Flutter
        $this->sendJsonSuccess([
            'token' => $jwt,
            'user'  => [
                'id'      => $existingUser['id'],
                'name'    => $existingUser['name'],
                'email'   => $existingUser['email'],
                'picture' => $existingUser['picture']
            ]
        ], 'Login berhasil');
    }
}