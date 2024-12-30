<?php
include('includes/database.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery</title>
    <link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
    <script src="https://unpkg.com/video.js/dist/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .media-container {
            margin: 10px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
            float: left;
            width: calc(33.33% - 20px);
        }

        .media-container:hover {
            transform: scale(1.05);
        }

        img {
            width: 100%;
            height: auto;
            display: block;
        }

        h2 {
            text-align: center;
            margin: 10px 0;
            font-size: 1.2em;
        }

        .control-buttons {
            text-align: center;
            margin-top: 10px;
        }

        .control-buttons button {
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .control-buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <?php
    // Récupérer les données des podcasts
    $query_podcasts = mysqli_query($con, "SELECT * FROM podcast_audio");
    $podcasts = [];
    while ($podcast = mysqli_fetch_assoc($query_podcasts)) {
        $podcasts[] = $podcast;
    }

    // Récupérer les données des vidéos en streaming
    $query_streams = mysqli_query($con, "SELECT * FROM stream_videos");
    $streams = [];
    while ($stream = mysqli_fetch_assoc($query_streams)) {
        $streams[] = $stream;
    }
    ?>

    <div id="mediaGallery">
        <?php foreach ($podcasts as $podcast) : ?>
            <div class="media-container">
                <h2><?php echo $podcast['title']; ?></h2>
                <?php if ($podcast['attachment']) : ?>
                    <?php if (strpos($podcast['attachment'], 'youtube.com') !== false) : ?>
                        <iframe width="100%" height="auto" src="<?php echo $podcast['attachment']; ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    <?php else : ?>
                        <audio id="audio_<?php echo $podcast['id']; ?>" controls autoplay>
                            <source src="<?php echo $podcast['attachment']; ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <div class="control-buttons">
                            <button onclick="document.getElementById('audio_<?php echo $podcast['id']; ?>').play()">Play</button>
                            <button onclick="document.getElementById('audio_<?php echo $podcast['id']; ?>').pause()">Pause</button>
                        </div>
                    <?php endif; ?>
                <?php elseif ($podcast['audio_url']) : ?>
                    <audio id="audio_<?php echo $podcast['id']; ?>" controls autoplay>
                        <source src="<?php echo $podcast['audio_url']; ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <div class="control-buttons">
                        <button onclick="document.getElementById('audio_<?php echo $podcast['id']; ?>').play()">Play</button>
                        <button onclick="document.getElementById('audio_<?php echo $podcast['id']; ?>').pause()">Pause</button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php foreach ($streams as $stream) : ?>
            <div class="media-container">
                <h2><?php echo $stream['title']; ?></h2>
                <?php if ($stream['attachment']) : ?>
                    <img src="<?php echo $stream['attachment']; ?>" alt="<?php echo $stream['title']; ?>">
                    <video id="video_<?php echo $stream['id']; ?>" class="video-js vjs-default-skin" controls autoplay>
                        <source src="<?php echo $stream['attachment']; ?>" type="application/x-mpegURL">
                        Your browser does not support the video tag.
                    </video>
                    <div class="control-buttons">
                        <button onclick="document.getElementById('video_<?php echo $stream['id']; ?>').play()">Play</button>
                        <button onclick="document.getElementById('video_<?php echo $stream['id']; ?>').pause()">Pause</button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            if (Hls.isSupported()) {
                                var video = document.getElementById('video_<?php echo $stream['id']; ?>');
                                var hls = new Hls();
                                hls.loadSource('<?php echo $stream['attachment']; ?>');
                                hls.attachMedia(video);
                                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                    video.play();
                                });
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = '<?php echo $stream['attachment']; ?>';
                                video.addEventListener('loadedmetadata', function() {
                                    video.play();
                                });
                            }
                        });
                    </script>
                <?php elseif ($stream['video_url']) : ?>
                    <img src="<?php echo $stream['attachment']; ?>" alt="<?php echo $stream['title']; ?>">
                    <video id="video_<?php echo $stream['id']; ?>" class="video-js vjs-default-skin" controls autoplay>
                        <source src="<?php echo $stream['video_url']; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="control-buttons">
                        <button onclick="document.getElementById('video_<?php echo $stream['id']; ?>').play()">Play</button>
                        <button onclick="document.getElementById('video_<?php echo $stream['id']; ?>').pause()">Pause</button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var player = videojs('video_<?php echo $stream['id']; ?>');
                            player.ready(function() {
                                player.play(); // Auto-play the video
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>
