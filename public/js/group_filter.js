document.addEventListener('DOMContentLoaded', function () {
    const filterToggle = document.getElementById('filter-toggle');
    const filterOptions = document.getElementById('filter-options');
    const filterSelect = document.getElementById('group-filter');
    const groupCards = document.querySelectorAll('.group-card');
    let selectedFilter = 'all';

    filterToggle.addEventListener('click', function () {
        filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
    });

    filterOptions.addEventListener('click', function (event) {
        if (event.target.classList.contains('filter-option')) {
            selectedFilter = event.target.getAttribute('data-value');
            filterGroups(selectedFilter);
            updateFilterOptions(selectedFilter);
            filterOptions.style.display = 'none';
        }
    });

    function filterGroups(filterValue) {
        groupCards.forEach(card => {
            const groupStatus = card.getAttribute('data-group-status');

            if (filterValue === 'all' || filterValue === groupStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function updateFilterOptions(selectedValue) {
        const options = document.querySelectorAll('.filter-option');
        options.forEach(option => {
            if (option.getAttribute('data-value') === selectedValue) {
                option.innerHTML = option.innerHTML + ' <i class="fas fa-check"></i>';
            } else {
                option.innerHTML = option.innerHTML.replace(' <i class="fas fa-check"></i>', '');
            }
        });
    }

    // Inicializar com o filtro "all"
    filterGroups(selectedFilter);
    updateFilterOptions(selectedFilter);
});