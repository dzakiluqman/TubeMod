<?php

class HistoryController extends Controller {

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASEURL . "/auth/login");
            exit;
        }
    }

    public function index()
    {
        $historyModel = $this->model('HistoryModel');

        $data['history'] = $historyModel->getByUser($_SESSION['user_id']);

        $this->view('history', $data);
        $this->view('layouts/footer');
    }
}