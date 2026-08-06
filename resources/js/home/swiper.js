import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

const heroSwiper = document.querySelector('.heroSwiper');

if (heroSwiper) {
    new Swiper(heroSwiper, {
        loop: true,
        slidesPerView: 1,
        centeredSlides: false,

        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
}