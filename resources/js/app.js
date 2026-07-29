import '../sass/app.scss';
import '../css/home/home.css';
import 'bootstrap';

import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// reservation pages
import './reservations/seat-selection';
import './reservations/ticket-type';
import './reservations/payment-method';
import './reservations/showtime-selection';

//admin dashboard
import './admin/chart.js';


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

//navbar
const menuBtn = document.getElementById("menuBtn");
const closeBtn = document.getElementById("closeBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

if (menuBtn) {
    menuBtn.addEventListener("click", () => {
        sidebar.classList.add("active");
        overlay.classList.add("active");
    });

    closeBtn.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    });

    overlay.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    });
}