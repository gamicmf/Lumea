document.addEventListener('DOMContentLoaded', function () {
    const inviteInput = document.getElementById('add-member-input');
    const dropdown = document.createElement('div');
    dropdown.classList.add('dropdown-menu');
    dropdown.style.position = 'absolute';
    dropdown.style.background = 'white';
    dropdown.style.border = '1px solid #ccc';
    dropdown.style.zIndex = '1000';
    dropdown.style.display = 'none';
    inviteInput.parentNode.appendChild(dropdown);

    inviteInput.addEventListener('input', function () {
        const query = inviteInput.value;
        if (query.length > 2) {
            fetch(`/users/autocomplete?username=${query}`)
                .then(response => response.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(user => {
                            const item = document.createElement('div');
                            item.classList.add('dropdown-item');
                            item.textContent = user.username;
                            item.addEventListener('click', function () {
                                inviteInput.value = user.username;
                                dropdown.style.display = 'none';
                            });
                            dropdown.appendChild(item);
                        });
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                });
        } else {
            dropdown.style.display = 'none';
        }
    });

    document.addEventListener('click', function (event) {
        if (!inviteInput.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
});