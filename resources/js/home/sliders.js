function bindSlider(buttonId, targetId, distance) {
    const button = document.getElementById(buttonId);
    const track = document.getElementById(targetId);

    if (!button || !track) {
        return;
    }

    button.addEventListener('click', () => {
        track.scrollBy({
            left: distance,
            behavior: 'smooth',
        });
    });
}

bindSlider('nowShowingPrevBtn', 'nowShowingSlider', -280);
bindSlider('nowShowingNextBtn', 'nowShowingSlider', 280);
bindSlider('comingSoonNextBtn', 'comingSoonSlider', 280);