<?php

namespace NowPlaying\API;

use SpotifyWebAPI;

class Spotify
{
    private $api;
    private $session;
    private $accessToken;
    private $refreshToken;
    private $scope = [
        'playlist-read-private',
        'user-read-private',
        'user-read-currently-playing',
        'user-read-playback-state',
        'user-read-recently-played'
    ];
    private $options = [
        'auto_refresh' => true,
    ];
    private $tracks;
    private $accessTokenFile = '.spotify-access-tokens';

    /**
     * Construct
     */
    public function __construct()
    {
        // Start session
        $this->session = new SpotifyWebAPI\Session(
            SPOTIFY_CLIENT_ID,
            SPOTIFY_CLIENT_SECRET,
            SPOTIFY_REDIRECT_URI
        );

        $this->readStoredTokens();

        // Get access token, authorize if not found
        if (!$this->getAccessToken()) {
            $this->authorize();
        }

        // Set access tokens
        $this->session->setAccessToken($this->getAccessToken());
        $this->session->setRefreshToken($this->getRefreshToken());

        // Start API
        $this->api = new SpotifyWebAPI\SpotifyWebAPI(
            $this->options,
            $this->session
        );

        // Update saved access tokens
        $this->updateSavedAccessTokens();
    }

    /**
     * Read stored tokens
     */
    private function readStoredTokens()
    {
        // Get tokens from file
        $json = file_get_contents($this->accessTokenFile);

        if (!$json) {
            return false;
        }

        $tokens = json_decode($json);

        // Store tokens
        $this->accessToken = $tokens->access;
        $this->refreshToken = $tokens->refresh;
    }

    /**
     * Get access token
     *
     * @return string
     */
    private function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get refresh token
     *
     * @return string
     */
    private function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Update saved access tokens
     */
    private function updateSavedAccessTokens()
    {
        file_put_contents(
            dirname(dirname(__DIR__)).'/'.$this->accessTokenFile,
            json_encode([
                'access' => $this->session->getAccessToken(),
                'refresh' => $this->session->getRefreshToken()
            ])
        );
    }
    
    /**
     * Authorize and redirect to Spotify authorization URL
     */
    public function authorize()
    {
        $state = $this->session->generateState();

        $options = [
            'scope' => $this->scope,
            'state' => $state,
        ];

        header('Location: ' . $this->session->getAuthorizeUrl($options));
    }

    /**
     * Get recent tracks
     *
     * @return array
     */
    public function getRecentTracks()
    {
        // Get recent tracks from Spotify Web API
        $tracks = $this->api->getMyRecentTracks();

        for ($i = 0; $i < 11; $i++) {
            $this->tracks[] = [
                'album' => $tracks->items[$i]->track->album->name,
                'image' => $tracks->items[$i]->track->album->images[1]->url,
                'artist' => $tracks->items[$i]->track->artists[0]->name,
                'name' => $tracks->items[$i]->track->name,
                'playing' => false,
                'url' => $tracks->items[$i]->track->external_urls->spotify
            ];
        }

        return $this->tracks;
    }

    /**
     * Get user info
     *
     * @return array
     */
    public function getUserInfo()
    {
        // Get user information from Spotify Web API
        $userInfo = $this->api->me();

        return array(
            'username' => $userInfo->display_name,
            'image' => $userInfo->images[0]->url,
            'url' => $userInfo->external_urls->spotify,
            'followers' => $userInfo->followers->total
        );
    }

    /**
     * Get current track
     *
     * @return array
     */
    public function getCurrentTrack()
    {
        // Get current track from Spotify Web API
        $track = $this->api->getMyCurrentTrack();

        // No current track, get first track of recently played tracks
        if (!$track) {
            $this->getRecentTracks();
            return $this->tracks[0];
        }

        return [
            'album' => $track->item->album->name,
            'image' => $track->item->album->images[1]->url,
            'artist' => $track->item->artists[0]->name,
            'name' => $track->item->name,
            'playing' => $track->is_playing,
            'url' => $track->item->external_urls->spotify
        ];
    }
}
