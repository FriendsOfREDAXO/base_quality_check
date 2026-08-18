document.addEventListener('click', (event) => {
    if (event.target.closest('[data-bqc-print]')) {
        window.print();
    }
});
