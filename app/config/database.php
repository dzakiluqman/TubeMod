<?php

function getConnection() {
    // Mengambil data dari config.php
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    return $conn;
}