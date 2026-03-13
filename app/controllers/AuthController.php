<?php

class AuthController extends Controller {

    public function login()
    {
        require_once '../app/config/google.php';

        $login_url = $google_client->createAuthUrl();

        $this->view('login', [
            'login_url' => $login_url
        ]);
    }

    public function googleCallback()
    {
        require_once '../app/config/google.php';

        if(isset($_GET['code']))
        {
            $token = $google_client
                ->fetchAccessTokenWithAuthCode($_GET['code']);

            $google_client->setAccessToken($token['access_token']);

            $service = new Google_Service_Oauth2($google_client);

            $user = $service->userinfo->get();

            $_SESSION['user'] = $user->email;
            $_SESSION['name'] = $user->name;
            $_SESSION['picture'] = $user->picture;

            header("Location: " . BASEURL . "/home");
            exit;
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();

        header("Location: " . BASEURL . "/home");
        exit;
    }
}