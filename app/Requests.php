<?php

namespace NowPlaying;

use NowPlaying\API\Spotify as SpotifyApi;

class Requests
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Catch getRecentTrack request, url/?getRecentTrack=1
        if (isset($_REQUEST['getRecentTrack'])) {
            $this->handleGetRecentTrackRequest();
        }
    }

    /**
     * Handle get recent track request
     */
    private function handleGetRecentTrackRequest()
    {
        $api = new SpotifyApi();
        $track = $api->getCurrentTrack();

        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($track);
        exit;
    }
}
