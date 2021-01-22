$(function() {

    if ( $('#slider-referencje').length > 0 ) {
        new Glide('#slider-referencje', {
            type: 'carousel',
            startAt: 0,
            perView: 1,
            autoplay: 2500
        }).mount();
    }

    if ( $('#slider-metamorfozy').length > 0 ) {
        new Glide('#slider-metamorfozy', {
            type: 'carousel',
            startAt: 0,
            perView: 2,
            autoplay: 2500,
            breakpoints: {
                780: {
                    perView: 1
                }
            }
        }).mount();
    }
});
