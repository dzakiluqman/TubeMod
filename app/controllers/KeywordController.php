<?php

class KeywordController extends Controller {

    private $model;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login?error=session_expired');
            exit;
        }

        $this->model = $this->model('KeywordModel');
    }

    // READ
    public function index()
    {
        $user_id = $_SESSION['user_id'];

        $data['keywords'] = $this->model->getAllByUser($user_id);

        $this->view('keywords', $data);
        $this->view('layouts/footer');
    }

    // CREATE
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->model->add(
                $_SESSION['user_id'],
                $_POST['word'],
                $_POST['category']
            );

            header("Location: " . BASEURL . "/keyword");
            exit;
        }
    }

    // UPDATE
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->model->update(
                $_POST['id'],
                $_SESSION['user_id'],
                $_POST['word'],
                $_POST['category']
            );

            header("Location: " . BASEURL . "/keyword");
            exit;
        }
    }

    // DELETE
    public function delete($id)
    {
        $this->model->delete($id, $_SESSION['user_id']);

        header("Location: " . BASEURL . "/keyword");
        exit;
    }

    // SAVE FILTER SESSION
    public function save_filters()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['filter_non_original_fonts'] = isset($_POST['filter_non_original_fonts']);
        }
        header("Location: " . BASEURL . "/keyword");
        exit;
    }
}