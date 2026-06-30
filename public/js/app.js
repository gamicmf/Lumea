document.getElementById('add-member-input').addEventListener('input', function () {
  const query = this.value;
  if (query.length > 2) {
      fetch(`/users/search?query=${query}`)
          .then(response => response.json())
          .then(data => {
              const resultsContainer = document.getElementById('autocomplete-results');
              resultsContainer.innerHTML = '';
              data.forEach(user => {
                  const div = document.createElement('div');
                  div.classList.add('autocomplete-item');
                  div.textContent = user.username;
                  div.dataset.id = user.id;
                  div.addEventListener('click', function () {
                      document.getElementById('add-member-input').value = user.username;
                      resultsContainer.innerHTML = '';
                  });
                  resultsContainer.appendChild(div);
              });
          });
  }
});

function addMember(groupId) {
  const username = document.getElementById('add-member-input').value;
  fetch(`/groups/${groupId}/add-member`, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ username }),
  })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              alert('User added successfully!');
              location.reload(); // Atualiza a página para refletir as alterações
          } else {
              alert(data.message || 'An error occurred.');
          }
      });
}

function showSection(sectionId) {
  // Esconder todas as seções
  document.querySelectorAll('.section').forEach(section => {
      section.style.display = 'none';
  });

  // Mostrar a seção selecionada
  document.getElementById(sectionId + '-section').style.display = 'block';

  // Atualizar a classe ativa na navbar
  document.querySelectorAll('.nav-link').forEach(link => {
      link.classList.remove('active');
  });
  document.querySelector(`a[href="#${sectionId}"]`).classList.add('active');
}

// Example in app.js or a dedicated JS file
const input = document.getElementById('userSearch');
const resultsContainer = document.getElementById('searchResults');

input.addEventListener('input', async (e) => {
    const query = e.target.value;
    if (query.length > 2) {
        const response = await fetch(`/users/search?q=${query}`);
        const users = await response.json();
        resultsContainer.innerHTML = users.map(user => `
            <div data-id="${user.id}" class="user-result">${user.name}</div>
        `).join('');
    }
});

resultsContainer.addEventListener('click', (e) => {
    const target = e.target.closest('.user-result');
    if (target) {
        const userId = target.getAttribute('data-id');
        addUserToGroup(userId);
    }
});

async function addUserToGroup(userId) {
    const groupId = document.getElementById('groupId').value; // hidden input with group ID
    await fetch(`/groups/${groupId}/add`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId }),
    });
    alert('User added!');
}









