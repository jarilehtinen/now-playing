<?php

require '../vendor/autoload.php';
require '../auth.php';

$session = new SpotifyWebAPI\Session(
    SPOTIFY_CLIENT_ID,
    SPOTIFY_CLIENT_SECRET,
    SPOTIFY_REDIRECT_URI
);

$state = $_GET['state'];

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);

$accessToken = $session->getAccessToken();
$refreshToken = $session->getRefreshToken();

if (!$accessToken || !$refreshToken) {
    echo 'Error while getting access tokens';
    exit;
}

file_put_contents(
    '../.spotify-access-tokens',
    json_encode([
        'access' => $accessToken,
        'refresh' => $refreshToken
    ])
);

header('location: ../');
exit;
