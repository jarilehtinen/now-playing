# Now Playing

PHP/HTML/JavaScript app which displays currently playing song from Spotify along with additional information from LastFM.

When a song is not playing, app displays recently played tracks, total number of scrobbles etc.

![Screenshot of Now Playing](https://raw.githubusercontent.com/jarilehtinen/project-images/main/now-playing.jpg)

![Another screenshot of Now Playing](https://raw.githubusercontent.com/jarilehtinen/project-images/main/now-playing-2.jpg)

## Installation

1. Create an app on [Spotify for Developers](https://developer.spotify.com/dashboard/applications) site

   Note: make sure the *Redirect URI* is the same than the `SPOTIFY_REDIRECT_URI` below.

2. Create API account on [LastFM](https://www.last.fm/api/account/create)

   Note: you can leave the *Callback URL* empty.

3. Create `/auth.php` file containing following information:

   ```php
   <?php
   
   define('LASTFM_USER', 'YOUR-LASTFM-USERNAME');
   define('LASTFM_API_KEY', 'YOUR-LASTFM-API-KEY');
   
   define('SPOTIFY_CLIENT_ID', 'YOUR-CLIENT-ID');
   define('SPOTIFY_CLIENT_SECRET', 'YOUR-CLIENT-SECRET');
   define('SPOTIFY_REDIRECT_URI', '/spotify-callback/');
   
   # Warning: Now Playing saves Spotify access tokens to the file below,
   # make sure it is in a secure place
   define('SPOTIFY_ACCESS_TOKENS_FILE_PATH', 'YOUR-PATH-TO-SPOTIFY-ACCESS-TOKENS');
   ```

4. Run Composer `php composer.phar install` or if you have Composer installed globally `composer install` 

   Composer installs [Spotify Web API PHP](https://github.com/jwilsson/spotify-web-api-php). If you are not familiar with Composer, head to [getcomposer.org](https://getcomposer.org/download/).


## What, why?

I stumbled upon Jason Tate's [raspberry-pi-now-playing](https://github.com/jasontate/raspberry-pi-now-playing) which fetched currently playing song from LastFM and displayed it on a Hyperpixel screen. I started to make some little changes, and after a while I ended up adding Spotify API. Eventually I pretty much rewrote the whole thing.

Jason has [great instructions]( https://chorus.fm/news/now-playing-my-raspberry-pi-weekend-project/) how to get it (his version) running on a Raspberry Pi and Hyperpixel screen. Check that out.
