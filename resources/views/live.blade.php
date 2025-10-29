{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Stream</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #000;
        }

        video {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            background: black;
        }
    </style>
</head>
<body>

<video id="video" controls autoplay muted></video>

<script>
    const video = document.getElementById('video');
    const videoSrc ='{{ $streamUrl }}';

    if (Hls.isSupported()) {
        const hls = new Hls({
            liveDurationInfinity: true,
            maxLiveSyncPlaybackRate: 1.5
        });
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => {
            video.play();
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', () => {
            video.play();
        });
    }
</script>

</body>
</html> --}}
