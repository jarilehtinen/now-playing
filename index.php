<?php

require 'vendor/autoload.php';

if (!file_exists(__DIR__ . '/auth.php')) {
    echo 'auth.php file not found, see readme for instructions';
    exit;
}

require_once(__DIR__ . '/auth.php');
require_once('app/NowPlaying.php');
require_once('app/API/LastFM.php');
require_once('app/API/Spotify.php');
require_once('app/Requests.php');

use NowPlaying\NowPlaying;
use NowPlaying\Requests;

$requests = new Requests();
$app = new NowPlaying();

?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>
        <?php if ($app->isPlaying()) : ?>
            Now Playing - <?php echo $app->getTrackName(); ?> - <?php echo $app->getTrackArtist(); ?>
        <?php else : ?>
            Last Played
        <?php endif; ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript">
        var playing = <?php echo $app->isPlaying() ? 'true' : 'false'; ?>;
    </script>
    <script src="app.js"></script>
</head>
<body>
    <!-- Background artwork -->
    <div class="artwork" style="background-image: url('<?php echo $app->getTrackImage(); ?>')"></div>

    <!-- Now playing -->
    <div class="now-playing playing<?php echo !$app->isPlaying() ? ' hidden' : ''; ?>">
        <section class="main">
            <img class="image" src="<?php echo $app->getTrackImage(); ?>" alt="">
            <div class="text">
                <div class="track"><?php echo $app->getTrackName(); ?></div>
                <div class="artist"><?php echo $app->getTrackArtist(); ?></div>
                <div class="album"><?php echo $app->getTrackAlbum(); ?></div>
                <div class="meta">
                    <span class="scrobbles">
                        <?php echo $app->displayTrackScrobbles(); ?>
                    </span>
                    <span class="loved">
                        <?php echo $app->isTrackLoved() ? '&nbsp;&nbsp;❤️' : ''; ?>
                    </span>
                </div>
            </div>
        </section>
    </div>

    <!-- Last played view -->
    <div class="last-played<?php echo $app->isPlaying() ? ' hidden' : ''; ?>">
        <div class="last-played-main">
            <div class="statistics-user">
                <img src="<?php echo $app->getUserImage(); ?>" width="50" alt="" class="statistics-user-image">
                <span class="statistics-user-name"><?php echo $app->getUsername(); ?></span>
            </div>


            <div class="statistics-data">
                <?php if ($app->getUserFollowers()) : ?>
                    <div class="statistics statistics-followers">
                        <div class="statistics-header">Followers</div>
                        <div class="statistics-follower-count">
                            <?php echo $app->getUserFollowers(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($app->getUserScrobbles()) : ?>
                    <div class="statistics statistics-scrobbles">
                        <div class="statistics-header">Scrobbles</div>
                        <div class="statistics-scrobble-count">
                            <?php echo $app->getUserScrobbles(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="statistics statistics-last-played">
                    <div class="statistics-last-played-image" 
                        style="background-image:url('<?php echo $app->getTrackImage(); ?>')"></div>
                    <div class="statistics-last-played-info">
                        <div class="statistics-header">Last Played</div>
                        <div class="statistics-artist-track">
                            <div class="statistics-track"><?php echo $app->getTrackName(); ?></div>
                            <div class="statistics-artist"><?php echo $app->getTrackArtist(); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($app->getRecentTracks()) : ?>
                <div class="statistics-recent-tracks-title">Recent Tracks</div>
                <div class="statistics-recent-tracks">
                    <?php foreach ($app->getRecentTracks(1) as $track) { ?>
                        <?php if (!$app->isKiosk()) : ?>
                            <a href="<?php echo $track['url']; ?>">
                        <?php endif; ?>
                            <img class="statistics-recent-track" 
                                src="<?php echo $track['image']; ?>" 
                                title="<?php echo $track['artist']; ?> &ndash; <?php echo $track['name']; ?>" 
                                width="300">
                        <?php if (!$app->isKiosk()) : ?>
                            </a>
                        <?php endif; ?>
                    <?php } ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>