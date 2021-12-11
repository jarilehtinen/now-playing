<?php

require '../vendor/autoload.php';
require '../auth.php';

$session = new SpotifyWebAPI\Session(
    SPOTIFY_CLIENT_ID,
    SPOTIFY_CLIENT_SECRET,
    SPOTIFY_REDIRECT_URI
);

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);

$accessToken = $session->getAccessToken();
$refreshToken = $session->getRefreshToken();

if (!$accessToken || !$refreshToken) {
    echo 'Error while getting access tokens';
    exit;
}

// Store tokens in a file
$filePath = SPOTIFY_ACCESS_TOKENS_FILE_PATH;

if (!$filePath) {
    echo 'SPOTIFY_ACCESS_TOKENS_FILE_PATH not defined';
    exit;
}

file_put_contents(
    $filePath,
    json_encode([
        'access' => $accessToken,
        'refresh' => $refreshToken
    ])
);

// Check if file exists
if (!file_exists($filePath)) {
    echo $filePath.' is not writable?';
    exit;
}

header('location: ../');
exit;
