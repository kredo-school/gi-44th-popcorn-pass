//
import '../sass/app.scss'
import 'bootstrap'

//reservation pages
import './reservations/seat-selection';
import './reservations/ticket-type';
import './reservations/payment-method';
// import './reservations/reservation-confirm';
// import './reservations/reservation-complete';


new Swiper(".heroSwiper",{

    loop:true,

    centeredSlides:true,

    slidesPerView:1.2,

    spaceBetween:0,

    autoplay:{
        delay:4000,
        disableOnInteraction:false,
    },

    pagination:{
        el:".swiper-pagination",
        clickable:true,
    },

    navigation:{
        nextEl:".swiper-button-next",
        prevEl:".swiper-button-prev",
    },

});