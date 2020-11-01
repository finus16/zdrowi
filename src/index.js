import './style.scss';

$('.navbar-nav a').on('click', function(e){
    let url = $(this).attr('href');
    let pos = url.indexOf("#");
    let anchor = pos != -1 ? url.substring(pos + 1) : null;
    if(anchor){
        let target = $('#' + anchor);
        if(target.length != 0){
            e.preventDefault();
            $('.navbar-collapse').collapse('toggle');
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 800);
        }
    }
});

$('a .btn').on('click', function(e){
    let url = $(this).attr('href');
    let pos = url.indexOf("#");
    let anchor = pos != -1 ? url.substring(pos + 1) : null;
    if(anchor){
        let target = $('#' + anchor);
        if(target.length != 0){
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 800);
        }
    }
});