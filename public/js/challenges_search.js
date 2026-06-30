document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-challenges');
    const challengesContainer = document.getElementById('challenges-container');
    const loadingIndicator = document.getElementById('loading-indicator');

    // Função para buscar e renderizar os desafios
    const fetchChallenges = (query = '') => {
        // Mostrar o indicador de carregamento
        loadingIndicator.style.display = 'block';

        fetch(`/challenges/search?query=${query}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(challenges => {
            challengesContainer.innerHTML = ''; // Limpar os resultados anteriores

            // Ocultar o indicador de carregamento
            loadingIndicator.style.display = 'none';

            if (challenges.length > 0) {
                challenges.forEach(challenge => {
                    const challengeCard = `
                        <div class="challenge-card">
                            <h2>
                                ${challenge.private ? '<i class="fas fa-lock"></i>' : ''}
                                ${challenge.name}
                            </h2>
                            <p>${challenge.description}</p>
                            <p>Participants: ${challenge.num_participants || 0}</p>
                            <p>Created on: ${challenge.creation_date}</p>
                            <p>Ends on: ${challenge.end_date}</p>
                            <a href="/challenges/show/${challenge.id}" class="view-more-challenge">View More ></a>
                        </div>
                    `;
                    challengesContainer.insertAdjacentHTML('beforeend', challengeCard);
                });
            } else {
                challengesContainer.innerHTML = '<p>No challenges found</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            challengesContainer.innerHTML = '<p>Error while fetching challenges.</p>';
            loadingIndicator.style.display = 'none';
        });
    };

    // Evento de digitação na barra de pesquisa
    searchInput.addEventListener('keyup', function () {
        const query = this.value.trim();

        // Mostrar todos os desafios se a barra de pesquisa estiver vazia
        if (query === '') {
            fetchChallenges(); // Busca todos os desafios
            return;
        }

        // Iniciar busca apenas a partir do segundo caractere
        if (query.length > 1) {
            fetchChallenges(query);
        }
    });

    // Buscar todos os desafios ao carregar a página
    fetchChallenges();
});
