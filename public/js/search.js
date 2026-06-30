$(document).ready(function () {
    const searchInput = $('#username-search'); // Campo de busca
    const dropdown = $('#search-dropdown'); // Container do dropdown

    searchInput.on('input', function () {
        const query = $(this).val().trim(); // Valor digitado pelo usuário

        // Verifica se a consulta tem pelo menos 2 caracteres
        if (query.length < 2) {
            dropdown.hide(); // Esconde o dropdown se a entrada for muito curta
            return;
        }

        // Faz a requisição AJAX para buscar os usuários
        $.ajax({
            url: '/users/autocomplete', // URL da rota que retorna os usuários
            type: 'GET',
            data: { username: query }, // Dados enviados
            success: function (users) {
                dropdown.empty(); // Limpa o dropdown antes de preenchê-lo

                // Verifica se há resultados
                if (users.length === 0) {
                    dropdown.hide(); // Esconde o dropdown se não houver resultados
                    return;
                }

                // Preenche o dropdown com os resultados
                users.forEach(function (user) {
                    const link = $('<a></a>')
                        .attr('href', '/profile/username/' + user.username) // Link para o perfil do usuário
                        .text(user.username) // Nome do usuário
                        .addClass('dropdown-item'); // Classe CSS para estilização
                    dropdown.append(link);
                });

                dropdown.show(); // Mostra o dropdown
            },
            error: function (xhr, status, error) {
                console.error('Erro ao buscar usuários:', error);
            },
        });
    });

    // Esconde o dropdown quando o usuário clica fora dele
    $(document).on('click', function (event) {
        if (!$(event.target).closest(searchInput).length && !$(event.target).closest(dropdown).length) {
            dropdown.hide();
        }
    });

    // Ao pressionar Enter, faz o redirecionamento
    searchInput.on('keypress', function (e) {
        if (e.which === 13) { // Verifica se a tecla pressionada foi Enter (13)
            const username = searchInput.val().trim();
            if (username.length >= 2) {
                window.location.href = '/profile/username/' + username; // Redireciona para o perfil
            }
            e.preventDefault(); // Impede que o formulário seja enviado
        }
    });
});
