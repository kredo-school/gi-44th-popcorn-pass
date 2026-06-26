document.addEventListener('DOMContentLoaded', function () {

    const ticketButtons = document.querySelectorAll('.ticket-type-btn');
    const ticketOptions = document.querySelectorAll('.ticket-option');

    // 現在選択中のボタン
    let currentButton = null;

    // 「SELECT TICKET TYPE」を押したとき
    ticketButtons.forEach(button => {
        button.addEventListener('click', function () {
            currentButton = this;
        });
    });

    // モーダル内で券種を選択したとき
    ticketOptions.forEach(option => {
        option.addEventListener('click', function () {

            const ticketName = this.dataset.ticket;

            if (currentButton) {
                currentButton.textContent = ticketName;
            }

            // モーダルを閉じる
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('ticketTypeModal')
            );
            modal.hide();
        });
    });

});
// document.addEventListener('DOMContentLoaded', function () {

//     const modal = document.getElementById('ticketTypeModal');

//     let currentSeat = null;
//     let selectedData = {};

//     modal.addEventListener('show.bs.modal', function (event) {

//         const button = event.relatedTarget;

//         currentSeat = button.getAttribute('data-seat');
//         const isPremium = button.getAttribute('data-premium') === '1' || button.getAttribute('data-premium') === 'true';

//         modal.querySelector('.selected-seat').textContent = currentSeat;

//         const premiumLabel = modal.querySelector('.premium-info');
//         if (premiumLabel) {
//             premiumLabel.style.display = isPremium ? 'block' : 'none';
//         }
//     });

//     document.addEventListener('click', function (e) {

//         if (!e.target.classList.contains('ticket-option-btn')) return;

//         e.preventDefault();

//         const ticketType = e.target.getAttribute('data-type');
//         const price = e.target.getAttribute('data-price');

//         if (!currentSeat) return;

//         selectedData[currentSeat] = {
//             type: ticketType,
//             price: price
//         };

//         console.log('selected:', selectedData);

//         updateSummary();
//         updateNextButton();

//         const bsModal = bootstrap.Modal.getInstance(modal);
//         bsModal.hide();
//     });

//     function updateSummary() {

//         const container = document.getElementById('selected-seats');

//         if (Object.keys(selectedData).length === 0) {
//             container.innerHTML = '<p>No seats selected</p>';
//             return;
//         }

//         let html = '';

//         let total = 0;

//         for (const seat in selectedData) {

//             const data = selectedData[seat];

//             total += Number(data.price);

//             html += `
//                 <div class="d-flex justify-content-between">
//                     <span>${seat} (${data.type})</span>
//                     <span>$${data.price}</span>
//                 </div>
//             `;
//         }

//         container.innerHTML = html;

//         document.querySelector('.total-price').textContent = `$${total.toFixed(2)}`;
//     }

//     function updateNextButton() {

//         const btn = document.getElementById('next-btn');

//         if (Object.keys(selectedData).length > 0) {
//             btn.disabled = false;
//         } else {
//             btn.disabled = true;
//         }
//     }

// });