<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../app/config/config.php';
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';

$app = new App();