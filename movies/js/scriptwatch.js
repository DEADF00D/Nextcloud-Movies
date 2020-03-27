$(function(){
    const player = new Plyr('#player');

    $(".sidebar").hover(function(){
        $(this).animate({
            right:'0px'
        }, 300, function(){
            $(this).css("right", "0px");
        });
    }, function(){
        $(this).animate({
            right:'-340px'
        }, 300, function(){
            $(this).css("right", "-340px");
        });
    });

    var movieUrl = OC.generateUrl(`/apps/movies/api/v1/movie/${ $(".sidebar").data("movie-path") }`);
    $.getJSON(movieUrl, function(emp){
        userscore_class = Movies.userscoreToClass(emp['movie']['TMDb']['userscore']);

        $(".sidebar .sidebar-body").html(`
        <h2 class="title">${ emp['movie']['TMDb']['title'] }</h2>
        <span class="releasedate">(${ emp['movie']['TMDb']['releasedate'] })</span>
        <p class="description">${ emp['movie']['TMDb']['description'] }</p>
        <h2 class="runtime">${ emp['movie']['TMDb']['runtime'] }</h2>
        <span class="userscore ${userscore_class}">${ emp['movie']['TMDb']['userscore'] }%</span>
        `);
    });
});
