<?php

namespace NowPlaying\API;

class LastFM
{
    /**
     * Get top albums
     *
     * @return array
     */
    public function getTopAlbums()
    {
        $endpoint = 'https://ws.audioscrobbler.com/2.0/?method=user.gettopalbums&user=' .
        LASTFM_USER . '&api_key=' .
        LASTFM_API_KEY . '&period=7day&limit=12&format=json';

        $result = $this->apiCall($endpoint);

        // Get the top 12 albums and their artwork
        $albums = array();

        for ($i = 0; $i < 11; $i++) {
            $albums[] = array(
                'image' => $result['topalbums']['album'][$i]['image'][3]['#text'],
                'artist' => $result['topalbums']['album'][$i]['artist']['name'],
                'name' => $result['topalbums']['album'][$i]['name']
            );
        }

        return $albums;
    }

    /**
     * Get user info
     *
     * @return array
     */
    public function getUserInfo()
    {
        $endpoint = 'https://ws.audioscrobbler.com/2.0/?method=user.getinfo&user=' .
        LASTFM_USER . '&api_key=' .
        LASTFM_API_KEY . '&format=json';

        $userInfo = $this->apiCall($endpoint);

        return array(
            'username_lastfm' => $userInfo['user']['realname'],
            'scrobbles' => number_format($userInfo['user']['playcount'], 0, '', ' ')
        );
    }

    /**
     * Get current track
     *
     * @return array
     */
    public function getCurrentTrack()
    {
        $endpoint = 'https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks' .
            '&user=' . LASTFM_USER .
            '&api_key=' . LASTFM_API_KEY .
            '&limit=2' .
            '&format=json';

        // Get tracks from API
        $tracks = $this->apiCall($endpoint);

        // Get last track from result
        $lastTrack = $tracks['recenttracks']['track'][0];

        // Get the album artwork
        $image = $lastTrack['image'][3]['#text'];

        if ($image) {
            // Change the size to the largest available
            $image = str_replace('300x300', '600x600', $image);
        } else {
            $image = 'artwork-placeholder.png';
        }

        // Populate track array with the returned information
        $track = [
            'name'      => $lastTrack['name'],
            'album'     => $lastTrack['album']['#text'],
            'artist'    => $lastTrack['artist']['#text'],
            'image'     => $image,
            'scrobbles' => 0,
            'playing'   => isset($lastTrack['@attr']['nowplaying'])
        ];

        // Get additional track info
        if ($track['playing']) {
            $trackInfo = $this->getTrackInfo($track['artist'], $track['name']);

            $track['scrobbles'] = $trackInfo['playCount'];
            $track['loved'] = $trackInfo['loved'] ? 1 : 0;
        }

        return $track;
    }

    /**
     * Get track info
     *
     * @param   string  $artist  Artist name
     * @param   string  $track   Track name
     * @return  array
     */
    public function getTrackInfo($artist, $track)
    {
        $endpoint = 'https://ws.audioscrobbler.com/2.0/?method=track.getInfo' .
            '&username=' . LASTFM_USER .
            '&api_key=' . LASTFM_API_KEY .
            '&artist=' . urlencode($artist) .
            '&track=' . urlencode($track) .
            '&format=json';

        $trackInfo = $this->apiCall($endpoint);

        if (isset($trackInfo['error'])) {
            return false;
        }

        return array(
            'playCount' => $trackInfo['track']['userplaycount'],
            'loved' => $trackInfo['track']['userloved']
        );
    }

    /**
     * API call
     *
     * @param   string  $endpoint  Endpoint URL
     * @return  array
     */
    private function apiCall($endpoint)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        curl_close($ch);

        return $result;
    }
}
