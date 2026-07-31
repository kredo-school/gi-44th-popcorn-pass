document.addEventListener('DOMContentLoaded', () => {
    history.pushState(null, null, location.href);

    window.addEventListener('popstate', () => {
        history.pushState(null, null, location.href);
    });
});