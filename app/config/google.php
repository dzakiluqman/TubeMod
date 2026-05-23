<?php

$google_client = new Google_Client();

$google_client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$google_client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);

$google_client->setRedirectUri(BASEURL . '/auth/googleCallback');

$google_client->addScope("email");
$google_client->addScope("profile");
$google_client->addScope("https://www.googleapis.com/auth/youtube.force-ssl");