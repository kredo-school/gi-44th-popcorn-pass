import '../sass/app.scss';
import 'bootstrap';

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// reservation pages
import './reservations/seat-selection';
import './reservations/ticket-type';
import './reservations/payment-method';


new Swiper(".heroSwiper", {
    loop: true,
    slidesPerView: 1,
    centeredSlides: false,

    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },

    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});