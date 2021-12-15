/* jshint esversion: 6 */
function getRecentTrack() {
    var url = document.location + '?getRecentTrack=1';

    var request = new Request(url, {
        "method": "GET",
    });

    return fetch(request);
}

function successHandler(value) {
    value.json().then(track => {
        // Track is playing, but viewing Last Played
        if (track.playing && !playing) {
            console.log('Start playing');
            updateNowPlaying(track);
        }

        // Track has changed
        if (track.playing && playing) {
            var currentTrack = document.querySelector(".track").innerText;

            if (track.name != currentTrack) {
                console.log('Update track');
                updateNowPlaying(track);
            }
        }

        // Track stopped playing
        if (!track.playing) {
            console.log('Stop playing');
            stopPlaying(track);
        }
    });

    setTimeout(tick, 15000);
}

function failureHandler(reason) {
    console.log(reason);
    setTimeout(tick, 60000);
}

function updateNowPlaying(track) {
    playing = true;

    document.title = 'Now playing - ' + track.name + ' - ' + track.artist;

    document.querySelector('.track').innerHTML = track.name;
    document.querySelector('.artist').innerHTML = track.artist;
    document.querySelector('.album').innerHTML = track.album;
    document.querySelector('.image').src = track.image;
    document.querySelector('.artwork').style.backgroundImage = 'url(' + track.image + ')';

    if (track.scrobbles == 1) {
        document.querySelector('.scrobbles').innerHTML = track.scrobbles + ' play';
    } else if (track.scrobbles > 1) {
        document.querySelector('.scrobbles').innerHTML = track.scrobbles + ' plays';
    } else {
        document.querySelector('.scrobbles').innerHTML = '';
    }

    if (track.loved) {
        document.querySelector('.loved').innerHTML = '&nbsp;&nbsp;❤️';
    } else {
        document.querySelector('.loved').innerHTML = '';
    }

    document.querySelector('.now-playing').classList.remove('hidden');
    document.querySelector('.last-played').classList.add('hidden');
}

function stopPlaying(track) {
    document.querySelector('.statistics-track').innerHTML = track.name;
    document.querySelector('.statistics-artist').innerHTML = track.artist;

    document.querySelector('.now-playing').classList.add('hidden');
    document.querySelector('.last-played').classList.remove('hidden');

    document.title = 'Last Played';

    playing = false;
}

function tick() {
    var rq = getRecentTrack();

    rq.then(successHandler, failureHandler);
    rq.catch(failureHandler);
}

(function() {
    setTimeout(tick, 15000);
})();
