<?php

class App {

    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseURL();
        $isApi = false;

        // Cek apakah request ditujukan untuk API
        if (isset($url[0]) && $url[0] === 'api') {
            $isApi = true;
            array_shift($url); // Hapus 'api' dari array URL

            // Tentukan nama controller API
            $controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : 'DefaultApiController';
            
            if (file_exists('app/controllers/api/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
            
            require_once 'app/controllers/api/' . $this->controller . '.php';

        } else {
            // Handle web requests (tetap mempertahankan versi web)
            $controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : $this->controller;
            if (file_exists('app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
            require_once 'app/controllers/' . $this->controller . '.php';
        }

        $this->controller = new $this->controller;

        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Set Headers khusus API
        if ($isApi) {
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json; charset=UTF-8');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            
            // Handle preflight request dari Flutter/Client
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                http_response_code(200);
                exit();
            }
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}