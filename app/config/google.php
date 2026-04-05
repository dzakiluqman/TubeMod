<?php

$google_client = new Google_Client();

$google_client->setClientId('329943591388-mcgfa4ed24f6e7bi1doah1abpf3fhqal.apps.googleusercontent.com');
$google_client->setClientSecret('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');

$google_client->setRedirectUri(
    BASEURL . '/auth/googleCallback'
);

$google_client->addScope("email");
$google_client->addScope("profile");
$google_client->addScope("https://www.googleapis.com/auth/youtube.force-ssl");