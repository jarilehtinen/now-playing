<?php

namespace NowPlaying;

use NowPlaying\API\Spotify as Spotify;
use NowPlaying\API\LastFM as LastFM;

class NowPlaying
{
    private $spotify;
    private $lastFm;
    private $track;
    private $tracks;
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Load APIs
        $this->spotify = new Spotify();
        $this->lastFm = new LastFM();

        // Get current track
        $this->getCurrentTrack();

        // Get user information
        $this->getUserInfo();

        // Get last played tracks
        $this->tracks = $this->spotify->getRecentTracks();
    }

    /**
     * Get current track
     */
    public function getCurrentTrack()
    {
        // Get current track from Spotify API
        $this->track = $this->spotify->getCurrentTrack();

        // Get play count and "loved" from LastFM API
        $info = $this->lastFm->getTrackInfo(
            $this->track['artist'],
            $this->track['name']
        );

        if ($info) {
            $this->track = array_merge($this->track, $info);
        }

        return $this->track;
    }

    /**
     * Get user info
     */
    private function getUserInfo()
    {
        // Get user info from Spotify
        $this->user = $this->spotify->getUserInfo();

        // Get LastFM username and total scrobble count from LastFM API
        $lastFmInfo = $this->lastFm->getUserInfo();

        if ($lastFmInfo) {
            $this->user = array_merge($this->user, $lastFmInfo);
        }

        return $this->user;
    }

    /**
     * Is playing
     *
     * @return boolean
     */
    public function isPlaying()
    {
        return $this->track['playing'];
    }

    /**
     * Get recent tracks
     *
     * @param  integer  $offset  Offset
     * @param  integer  $length  Length
     * @return array
     */
    public function getRecentTracks($offset = false, $length = false)
    {
        $tracks = $this->tracks;

        if ($offset > 0 && $length > 0) {
            return array_slice($tracks, $offset, $length);
        }

        if ($offset > 0) {
            return array_slice($tracks, $offset);
        }

        return $tracks;
    }

    /**
     * Track is loved
     *
     * @return boolean
     */
    public function isTrackLoved()
    {
        if (isset($this->track['loved'])) {
            return $this->track['loved'];
        }

        return false;
    }

    /**
     * Get track scrobbles
     *
     * @return mixed
     */
    public function getTrackScrobbles()
    {
        if (isset($this->track['scrobbles'])) {
            return $this->track['scrobbles'];
        }

        return false;
    }

    /**
     * Display track scrobbles
     */
    public function displayTrackScrobbles()
    {
        if (!$this->getTrackScrobbles()) {
            return '';
        }

        return $this->getTrackScrobbles() > 1 ? $this->getTrackScrobbles() . ' plays' : '1 play';
    }

    /**
     * Track name
     *
     * @return string
     */
    public function getTrackName()
    {
        return $this->track['name'];
    }

    /**
     * Get track artist
     *
     * @return string
     */
    public function getTrackArtist()
    {
        return $this->track['artist'];
    }

    /**
     * Get track album
     *
     * @return string
     */
    public function getTrackAlbum()
    {
        return $this->track['album'];
    }

    /**
     * Get track image
     *
     * @return string
     */
    public function getTrackImage()
    {
        return $this->track['image'];
    }

    /**
     * Username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->user['username'];
    }

    /**
     * Get user image
     *
     * @return string
     */
    public function getUserImage()
    {
        return $this->user['image'];
    }

    /**
     * Get user scrobbles
     *
     * @return integer
     */
    public function getUserScrobbles()
    {
        return $this->user['scrobbles'];
    }

    /**
     * Get user followers
     *
     * @return integer
     */
    public function getUserFollowers()
    {
        return $this->user['followers'];
    }
}
