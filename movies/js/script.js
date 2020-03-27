$(function(){
    var moviesUrl = OC.generateUrl('/apps/movies/api/v1/movies');
    $.getJSON(moviesUrl, function(emp){
        $("#movielist").html(``);
        if(emp.length>0){
            for(var i=0;i<emp.length;i++){
                userscore_class = Movies.userscoreToClass(emp[i]['movie']['TMDb']['userscore']);

                var movie=$(`<div class="movie-container">
                    <div class="movie-offset">
                        <div class="movie" data-id="${ emp[i]['fileid'] }">
                            <div class="movie-image">
                                <img src="/index.php/core/preview?fileId=${ emp[i]['movie']['LocalArt'] }&x=1764&y=1176&a=true" />
                                <div class="movie-body">
                                    <div class="movie-content">
                                        <h2 class="title">${ emp[i]['movie']['TMDb']['title'] }</h2>
                                        <span class="releasedate">(${ emp[i]['movie']['TMDb']['releasedate'] })</span>
                                        <p class="description">${ emp[i]['movie']['TMDb']['description'] }</p>
                                        <h2 class="runtime">${ emp[i]['movie']['TMDb']['runtime'] }</h2>
                                        <span class="userscore ${ userscore_class }">${ emp[i]['movie']['TMDb']['userscore'] }%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`).appendTo("#movielist");

                $(movie).find(".movie-body").css('opacity', 0);
                $(movie).hover(function(){
                    $(this).find(".movie-body").css('opacity', 0);
                    //$(this).find(".movie-content").css('top', 400);

                    $(this).find(".movie-body").animate({
                        opacity: 1
                    }, 300, function(){
                        $(this).find(".movie-body").css('opacity', 1);
                    });

                    /*
                    $(this).find(".movie-content").animate({
                        top:0
                    }, 200, function(){
                        $(this).find(".movie-body").css('top', 0);
                    });
                    */

                }, function(){
                    $(this).find(".movie-body").css('opacity', 1);
                    $(this).find(".movie-body").animate({
                        opacity: 0
                    }, 300, function(){
                        $(this).find(".movie-body").css('opacity', 0);
                    });
                });

                $(movie).find(".movie").click(function(){
                    window.location.href=OC.generateUrl('/apps/movies/watch/'+parseInt($(this).data("id")));
                });
            }
        }else{
            $("#movielist").append(`<div style="text-align:center; margin-top:10px;">
                <p style="color:white;">No movies detected on your cloud.</p>
                <p style="color:white;">Make sure they are correctly named then refresh this page.</p>
            </div>`);
        }
    });
});
