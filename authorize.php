<?php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    '8e53f1a2b45b4ca19746bec4fcd2afe4',
    '2cd5612a0ab041e0888d2aa3557cddb2',
    'http://localhost:8888/scoreboard/'
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    print_r($api->me());
} else {
    $options = [
        'scope' => [
            'user-read-email',
            'user-read-recently-played',
            'user-library-read',
            'user-library-modify',
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}
?>