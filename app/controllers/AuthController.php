<?php

class AuthController extends Controller {

    public function login()
    {
        require_once 'app/config/google.php';

        $login_url = $google_client->createAuthUrl();

        $this->view('login', [
            'login_url' => $login_url
        ]);
    }

    public function googleCallback()
    {
        require_once 'app/config/google.php';
        require_once 'app/models/UserModel.php';

        if (isset($_GET['code'])) {

            $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
            $google_client->setAccessToken($token['access_token']);

            $_SESSION['access_token'] = $token['access_token'];

            // ambil data user Google
            $service = new Google_Service_Oauth2($google_client);
            $user = $service->userinfo->get();

            $userModel = new UserModel();

            $existingUser = $userModel->getByGoogleId($user->id);

            if (!$existingUser) {
                $userModel->create([
                    'google_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture
                ]);

                $existingUser = $userModel->getByGoogleId($user->id);
            }

            $_SESSION['user_id'] = $existingUser['id'];

            $youtube = new Google_Service_YouTube($google_client);

            $channels = $youtube->channels->listChannels('snippet', [
                'mine' => true
            ]);

            if (count($channels->getItems()) > 0) {
                $channel = $channels->getItems()[0];

                $_SESSION['youtube_channel_id'] = $channel->getId();
            }

            header("Location: " . BASEURL . "/home");
            exit;
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: " . BASEURL . "/home");
        exit;
    }
}