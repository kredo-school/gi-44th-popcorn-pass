document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('ticketTypeModal');

    let currentSeat = null;
    let selectedData = {}; // seatごとの選択保持

    // モーダルを開いたとき「どの席か」を受け取る
    modal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        currentSeat = button.getAttribute('data-seat');
        const isPremium = button.getAttribute('data-premium') === '1' || button.getAttribute('data-premium') === 'true';

        modal.querySelector('.selected-seat').textContent = currentSeat;

        // premium表示制御（必要なら）
        const premiumLabel = modal.querySelector('.premium-info');
        if (premiumLabel) {
            premiumLabel.style.display = isPremium ? 'block' : 'none';
        }
    });

    // 券種ボタン（モーダル内）クリック処理
    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('ticket-option-btn')) return;

        e.preventDefault();

        const ticketType = e.target.getAttribute('data-type');
        const price = e.target.getAttribute('data-price');

        if (!currentSeat) return;

        // seatごとに保存
        selectedData[currentSeat] = {
            type: ticketType,
            price: price
        };

        console.log('selected:', selectedData);

        updateSummary();
        updateNextButton();

        // モーダル閉じる
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
    });

    // サマリー更新
    function updateSummary() {

        const container = document.getElementById('selected-seats');

        if (Object.keys(selectedData).length === 0) {
            container.innerHTML = '<p>No seats selected</p>';
            return;
        }

        let html = '';

        let total = 0;

        for (const seat in selectedData) {

            const data = selectedData[seat];

            total += Number(data.price);

            html += `
                <div class="d-flex justify-content-between">
                    <span>${seat} (${data.type})</span>
                    <span>$${data.price}</span>
                </div>
            `;
        }

        container.innerHTML = html;

        document.querySelector('.total-price').textContent = `$${total.toFixed(2)}`;
    }

    // NEXTボタン制御
    function updateNextButton() {

        const btn = document.getElementById('next-btn');

        if (Object.keys(selectedData).length > 0) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

});