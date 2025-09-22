// BuscaPet JavaScript Functions

// Global variables
let currentPage = 1;
let isLoading = false;

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize favorites
    initializeFavorites();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize image preview
    initializeImagePreview();
    
    // Initialize pagination
    initializePagination();
}

// Search functionality
function initializeSearch() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }
    
    const speciesFilter = document.getElementById('species');
    if (speciesFilter) {
        speciesFilter.addEventListener('change', performSearch);
    }
}

function performSearch() {
    if (isLoading) return;
    
    const search = document.getElementById('search')?.value || '';
    const species = document.getElementById('species')?.value || '';
    
    showLoading();
    
    const params = new URLSearchParams({
        search: search,
        species: species,
        page: 1
    });
    
    fetch(`api/pets.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePetsList(data.pets);
                updatePagination(data.pagination);
            } else {
                showMessage('Erro ao buscar pets: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            showMessage('Erro na conexão: ' + error.message, 'danger');
        })
        .finally(() => {
            hideLoading();
        });
}

function updatePetsList(pets) {
    const container = document.getElementById('petsContainer');
    if (!container) return;
    
    if (pets.length === 0) {
        container.innerHTML = '<div class="col-12 text-center"><p class="lead">Nenhum pet encontrado.</p></div>';
        return;
    }
    
    container.innerHTML = pets.map(pet => createPetCard(pet)).join('');
    
    // Reinitialize favorites for new cards
    initializeFavorites();
}

function createPetCard(pet) {
    const imageUrl = pet.image_url || 'assets/images/default-pet.jpg';
    const favoriteClass = pet.is_favorited ? 'favorited' : '';
    
    return `
        <div class="col-md-6 col-lg-4">
            <div class="card pet-card">
                <div class="position-relative">
                    <img src="${imageUrl}" class="card-img-top" alt="${pet.name}" onerror="this.src='assets/images/default-pet.jpg'">
                    <button class="favorite-btn ${favoriteClass}" onclick="toggleFavorite(${pet.id}, this)">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <h5 class="pet-name">${pet.name}</h5>
                    <span class="pet-species">${pet.species}</span>
                    <p class="pet-breed">${pet.breed || 'Raça não informada'}</p>
                    <p class="pet-age">Idade: ${pet.age || 'Não informada'}</p>
                    <p class="card-text">${pet.description || 'Sem descrição'}</p>
                    <small class="text-muted">Por: ${pet.owner_username}</small>
                </div>
            </div>
        </div>
    `;
}

// Favorites functionality
function initializeFavorites() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}

function toggleFavorite(petId, button) {
    if (isLoading) return;
    
    const isFavorited = button.classList.contains('favorited');
    const action = isFavorited ? 'remove' : 'add';
    
    button.disabled = true;
    
    fetch('api/favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            pet_id: petId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (action === 'add') {
                button.classList.add('favorited');
            } else {
                button.classList.remove('favorited');
            }
            showMessage(data.message, 'success');
        } else {
            showMessage(data.message, 'danger');
        }
    })
    .catch(error => {
        showMessage('Erro na conexão: ' + error.message, 'danger');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// Image preview functionality
function initializeImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            previewImage(e.target);
        });
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            let preview = document.getElementById('imagePreview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'imagePreview';
                preview.className = 'image-preview';
                input.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Pagination
function initializePagination() {
    const paginationContainer = document.getElementById('pagination');
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            e.preventDefault();
            if (e.target.tagName === 'A' && !e.target.classList.contains('disabled')) {
                const page = parseInt(e.target.getAttribute('data-page'));
                if (page) {
                    loadPage(page);
                }
            }
        });
    }
}

function loadPage(page) {
    if (isLoading || page === currentPage) return;
    
    currentPage = page;
    const search = document.getElementById('search')?.value || '';
    const species = document.getElementById('species')?.value || '';
    
    showLoading();
    
    const params = new URLSearchParams({
        search: search,
        species: species,
        page: page
    });
    
    fetch(`api/pets.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePetsList(data.pets);
                updatePagination(data.pagination);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showMessage('Erro ao carregar página: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            showMessage('Erro na conexão: ' + error.message, 'danger');
        })
        .finally(() => {
            hideLoading();
        });
}

function updatePagination(pagination) {
    const container = document.getElementById('pagination');
    if (!container) return;
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Anterior</a></li>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === pagination.current_page ? 'active' : '';
        paginationHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.current_page < pagination.total_pages) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Próximo</a></li>`;
    }
    
    paginationHTML += '</ul></nav>';
    container.innerHTML = paginationHTML;
}

// Utility functions
function showLoading() {
    isLoading = true;
    const loadingElement = document.getElementById('loading');
    if (loadingElement) {
        loadingElement.style.display = 'block';
    }
}

function hideLoading() {
    isLoading = false;
    const loadingElement = document.getElementById('loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
}

function showMessage(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer') || document.body;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alert, alertContainer.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Form submission handlers
function handleFormSubmit(formId, endpoint) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const formData = new FormData(form);
        
        showLoading();
        
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    form.reset();
                    form.classList.remove('was-validated');
                }
            } else {
                showMessage(data.message, 'danger');
            }
        })
        .catch(error => {
            showMessage('Erro na conexão: ' + error.message, 'danger');
        })
        .finally(() => {
            hideLoading();
        });
    });
}

// Initialize form handlers for login and register forms
if (document.getElementById('loginForm')) {
    handleFormSubmit('loginForm', 'api/auth.php');
}

if (document.getElementById('registerForm')) {
    handleFormSubmit('registerForm', 'api/auth.php');
}

if (document.getElementById('petForm')) {
    handleFormSubmit('petForm', 'api/pets.php');
}