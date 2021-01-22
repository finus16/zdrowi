import $ from 'jquery';
import Glide from '@glidejs/glide'
require('popper.js');


new Glide('#glide-courses', {
    type: 'carousel',
    startAt: 0,
    perView: 3,
    autoplay: 2500,
    breakpoints: {
        1140: {
            perView: 2
        },

        650: {
            perView: 1
        }
    }
}).mount();

new Glide('#glide-team', {
    type: 'carousel',
    startAt: 0,
    perView: 3,
    autoplay: 1500,
    breakpoints: {
        992: {
            perView: 2
        },

        650: {
            perView: 1
        }
    }
}).mount();