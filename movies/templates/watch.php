<?php
script('movies', 'movies');
script('movies', 'plyr');
script('movies', 'scriptwatch');

style('movies', 'plyr');
style('movies', 'style');
style('movies', 'stylewatch');
?>

<div id="app">
    <div class="sidebar" data-movie-path="<?php p($_['filename']); ?>">
        <div class="sidebar-body">
            <div class="icon-loading"></div>
        </div>
    </div>
	<video id="player" playsinline controls autoplay>
        <source src="/remote.php/dav/files/<?php p($_['userId'].$_['filename']); ?>" type="<?php echo $_['mimetype']; ?>">
    </video>
</div>
