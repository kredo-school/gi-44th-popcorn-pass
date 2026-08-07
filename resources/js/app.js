import 'bootstrap';


// Home
import './home/swiper';
import './home/sliders';
import './home/nearby-cinemas';

//Recommendations
import './common/recommendations';

// reservation pages
import './reservations/seat-selection';
import './reservations/ticket-type';
import './reservations/payment-method';
import './reservations/showtime-selection';
import './reservations/paypal-checkout';
import './reservations/reservation-complete';

//mypage
import './mypage/tickets';
import './mypage/profile-edit';

// Customer Chat
import './customer/chat';

// Community Discussion
import './movie-detail/community-discussion';

//Map
import './map/index';

//Shoetime display
import './showtime-display/index';


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
