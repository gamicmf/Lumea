document.addEventListener('DOMContentLoaded', function () {
    const filterToggle = document.getElementById('filter-toggle');
    const filterOptions = document.getElementById('filter-options');
    const filterOptionsItems = document.querySelectorAll('.filter-option');

    filterToggle.addEventListener('click', function () {
        filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
    });

    filterOptionsItems.forEach(option => {
        option.addEventListener('click', function () {
            filterOptionsItems.forEach(item => item.classList.remove('selected'));
            option.classList.add('selected');
            filterOptions.style.display = 'none';

            // Enviar solicitação de filtro ao servidor
            const filterValue = option.dataset.value;
            const url = new URL(window.location.href);
            url.searchParams.set('filter', filterValue);
            window.location.href = url.toString();
        });
    });
});