document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-groups');
    const groupCards = document.querySelectorAll('.group-card');

    // Função para filtrar e renderizar os grupos
    const filterGroups = (query = '') => {
        const lowerCaseQuery = query.toLowerCase();

        groupCards.forEach(card => {
            const groupName = card.getAttribute('data-group-name');
            if (groupName.includes(lowerCaseQuery)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    };

    // Função debounce para limitar a frequência das chamadas de busca
    const debounce = (func, delay) => {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    };

    // Evento de digitação na barra de pesquisa
    searchInput.addEventListener('keyup', debounce(function () {
        const query = this.value.trim();
        filterGroups(query);
    }, 300)); // 300ms de atraso

    // Filtrar todos os grupos ao carregar a página
    filterGroups();
});