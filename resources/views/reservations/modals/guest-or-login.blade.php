<div class="modal fade" id="guestLoginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Continue Reservation
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center">
                <p>
                    Please choose how you would like to continue.
                </p>
                <form action="{{ route('reservations.guest') }}" method="POST">
                    @csrf
                    <input type="hidden" name="showtime_id" id="selectedShowtimeId">
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Continue as Guest
                    </button>
                </form>

                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>