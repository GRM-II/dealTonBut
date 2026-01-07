function setThemeIcon() {
    const btn = document.getElementById('theme-toggle');
    if (btn) {
        if (document.body.classList.contains('dark-theme')) {
            btn.innerHTML = '🌙';
        } else {
            btn.innerHTML = '☀️';
        }
    }
}

function updateNavigationIcons() {
    const isDark = document.body.classList.contains('dark-theme');
    const tradeIcon = document.getElementById('trade-nav-icon');
    const marketIcon = document.getElementById('market-nav-icon');
    const homeIcon = document.getElementById('home-nav-icon');

    if (tradeIcon) {
        tradeIcon.src = isDark ? '/public/assets/img/Trade_Night.svg' : '/public/assets/img/Trade_Day.svg';
    }
    if (marketIcon) {
        marketIcon.src = isDark ? '/public/assets/img/Market_Night.svg' : '/public/assets/img/Market_Day.svg';
    }
    if (homeIcon) {
        homeIcon.src = isDark ? '/public/assets/img/Home_Night.svg' : '/public/assets/img/Home_Day.svg';
    }
}

function applySavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
    setThemeIcon();
    updateNavigationIcons();
}

function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
    setThemeIcon();
    updateNavigationIcons();
}

function initRegisterForm(dbUnavailable, dbMessage) {
    const form = document.getElementById('register-form');
    if (!form) return;

    if (dbUnavailable) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert(dbMessage);
        });
        return;
    }

    form.addEventListener('submit', function(e) {
        const pwd = document.getElementById('password').value;
        const confirm = document.getElementById('confirm-password').value;
        if (pwd !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
        }
    });
}

window.addEventListener('DOMContentLoaded', function() {
    applySavedTheme();

    // Auto-détection de la page et initialisation appropriée
    if (document.getElementById('delete-account-btn')) {
        // Page de profil détectée donc initialiser
        initProfilePage();
    } else if (document.getElementById('open-modal-btn')) {
        // Page marketplace détectée donc initialiser
        initMarketplace();
    } else if (document.getElementById('register-form')) {
        // Page register détectée donc initialiser
        const dbUnavailableElem = document.querySelector('.db-unavailable-message');
        const dbUnavailable = dbUnavailableElem !== null;
        const dbMessage = dbUnavailable ? dbUnavailableElem.textContent.trim() : '';
        initRegisterForm(dbUnavailable, dbMessage);
    }

    // Initialiser la modale de mot de passe oublié
    initForgotPasswordModal();
});

// Gestion de la modale mot de passe oublié
function initForgotPasswordModal() {
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    const forgotPasswordModal = document.getElementById('forgot-password-modal');
    const closeBtn = forgotPasswordModal ? forgotPasswordModal.querySelector('.close') : null;

    if (forgotPasswordLink && forgotPasswordModal) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();

            // Afficher le modal avec display flex pour le centrage
            forgotPasswordModal.style.display = 'flex';

            // Force un reflow pour que la transition CSS fonctionne
            void forgotPasswordModal.offsetHeight;

            // Démarrer l'animation de fade-in
            forgotPasswordModal.style.opacity = '1';
            const modalContent = forgotPasswordModal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                // Animation de fade-out
                forgotPasswordModal.style.opacity = '0';
                const modalContent = forgotPasswordModal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.transform = 'scale(0.9)';
                }
                // Masquer après l'animation (400ms)
                setTimeout(function() {
                    forgotPasswordModal.style.display = 'none';
                }, 400);
            });
        }

        // Fermer le modal si on clique en dehors
        forgotPasswordModal.addEventListener('click', function(e) {
            if (e.target === forgotPasswordModal) {
                // Animation de fade-out
                forgotPasswordModal.style.opacity = '0';
                const modalContent = forgotPasswordModal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.transform = 'scale(0.9)';
                }
                // Masquer après l'animation (400ms)
                setTimeout(function() {
                    forgotPasswordModal.style.display = 'none';
                }, 400);
            }
        });
    }
}

// Marketplace: Gestion du modal et du carrousel
function initMarketplace() {

    // Gestion du modal
    const modal = document.getElementById('offer-modal');
    const openBtn = document.getElementById('open-modal-btn');
    const closeBtn = document.getElementById('close-modal');

    if (openBtn && modal) {
        openBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Navigation carrousel
    document.querySelectorAll('.arrow-left, .arrow-btn[data-direction="left"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            const carousel = document.querySelector(`[data-carousel="${category}"]`);
            if (carousel) {
                carousel.scrollBy({ left: -400, behavior: 'smooth' });
            }
        });
    });

    document.querySelectorAll('.arrow-right, .arrow-btn[data-direction="right"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            const carousel = document.querySelector(`[data-carousel="${category}"]`);
            if (carousel) {
                carousel.scrollBy({ left: 400, behavior: 'smooth' });
            }
        });
    });

    // Recherche
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.offer-card').forEach(card => {
                const title = card.querySelector('.offer-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Filtrage par catégorie via la sidebar
    const categoryCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
    const allCheckbox = document.querySelector('.filter-option input[value="all"]');

    if (categoryCheckboxes.length > 0) {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Si "Toutes" est coché, décocher les autres
                if (this.value === 'all' && this.checked) {
                    categoryCheckboxes.forEach(cb => {
                        if (cb.value !== 'all') cb.checked = false;
                    });
                } else if (this.value !== 'all' && this.checked) {
                    // Si une catégorie spécifique est cochée, décocher "Toutes"
                    if (allCheckbox) allCheckbox.checked = false;
                }

                // Récupérer les catégories sélectionnées
                const selectedCategories = Array.from(categoryCheckboxes)
                    .filter(cb => cb.checked && cb.value !== 'all')
                    .map(cb => cb.value);

                // Filtrer les sections de catégories
                document.querySelectorAll('.category-section').forEach(section => {
                    const categoryName = section.querySelector('.category-name').textContent;

                    if (allCheckbox && allCheckbox.checked || selectedCategories.length === 0) {
                        // Afficher toutes les catégories
                        section.style.display = 'block';
                    } else if (selectedCategories.includes(categoryName)) {
                        // Afficher seulement les catégories sélectionnées
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
            });
        });
    }
}

// Profile: Gestion de l'édition des champs utilisateur
function initProfilePage() {
    // Edit username
    const editUsernameBtn = document.getElementById('edit-username-btn');
    const saveUsernameBtn = document.getElementById('save-username-btn');
    const cancelUsernameBtn = document.getElementById('cancel-username-btn');
    const usernameDisplay = document.getElementById('username-display');
    const usernameInput = document.getElementById('new_username');

    if (editUsernameBtn) {
        editUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = 'none';
            usernameInput.style.display = '';
            saveUsernameBtn.style.display = '';
            cancelUsernameBtn.style.display = '';
            editUsernameBtn.style.display = 'none';
            usernameInput.focus();
        });

        cancelUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = '';
            usernameInput.style.display = 'none';
            saveUsernameBtn.style.display = 'none';
            cancelUsernameBtn.style.display = 'none';
            editUsernameBtn.style.display = '';
            usernameInput.value = usernameDisplay.textContent;
        });
    }

    // Edit email
    const editEmailBtn = document.getElementById('edit-email-btn');
    const saveEmailBtn = document.getElementById('save-email-btn');
    const cancelEmailBtn = document.getElementById('cancel-email-btn');
    const emailDisplay = document.getElementById('email-display');
    const emailInput = document.getElementById('new_email');

    if (editEmailBtn) {
        editEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = 'none';
            emailInput.style.display = '';
            saveEmailBtn.style.display = '';
            cancelEmailBtn.style.display = '';
            editEmailBtn.style.display = 'none';
            emailInput.focus();
        });

        cancelEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = '';
            emailInput.style.display = 'none';
            saveEmailBtn.style.display = 'none';
            cancelEmailBtn.style.display = 'none';
            editEmailBtn.style.display = '';
            emailInput.value = emailDisplay.textContent;
        });
    }

    // Edit password
    const editPasswordBtn = document.getElementById('edit-password-btn');
    const savePasswordBtn = document.getElementById('save-password-btn');
    const cancelPasswordBtn = document.getElementById('cancel-password-btn');
    const passwordDisplay = document.getElementById('password-display');
    const passwordInput = document.getElementById('new_password');

    if (editPasswordBtn) {
        editPasswordBtn.addEventListener('click', function() {
            passwordDisplay.style.display = 'none';
            passwordInput.style.display = '';
            savePasswordBtn.style.display = '';
            cancelPasswordBtn.style.display = '';
            editPasswordBtn.style.display = 'none';
            passwordInput.focus();
        });

        cancelPasswordBtn.addEventListener('click', function() {
            passwordDisplay.style.display = '';
            passwordInput.style.display = 'none';
            savePasswordBtn.style.display = 'none';
            cancelPasswordBtn.style.display = 'none';
            editPasswordBtn.style.display = '';
            passwordInput.value = '';
        });
    }

    // Delete account modal
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const deleteModal = document.getElementById('delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    if (deleteAccountBtn && deleteModal) {
        deleteAccountBtn.addEventListener('click', function() {

            // Afficher le modal avec display flex pour le centrage
            deleteModal.style.display = 'flex';

            // Force un reflow pour que la transition CSS fonctionne
            void deleteModal.offsetHeight;

            // Démarrer l'animation de fade-in
            deleteModal.style.opacity = '1';
            const innerDiv = deleteModal.querySelector('div');
            if (innerDiv) {
                innerDiv.style.transform = 'scale(1)';
            }
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', function() {
                // Animation de fade-out
                deleteModal.style.opacity = '0';
                const innerDiv = deleteModal.querySelector('div');
                if (innerDiv) {
                    innerDiv.style.transform = 'scale(0.9)';
                }
                // Masquer après l'animation (400ms)
                setTimeout(function() {
                    deleteModal.style.display = 'none';
                }, 400);
            });
        }

        // Fermer le modal si on clique en dehors
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                // Animation de fade-out
                deleteModal.style.opacity = '0';
                const innerDiv = deleteModal.querySelector('div');
                if (innerDiv) {
                    innerDiv.style.transform = 'scale(0.9)';
                }
                // Masquer après l'animation (400ms)
                setTimeout(function() {
                    deleteModal.style.display = 'none';
                }, 400);
            }
        });
    }
}

// Bouton pour remonter en haut de la page
function initScrollToTop() {
    const scrollToTopBtn = document.getElementById('scroll-to-top-btn');

    if (scrollToTopBtn) {
        // Afficher/masquer le bouton lors du scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                scrollToTopBtn.style.opacity = '1';
                scrollToTopBtn.style.pointerEvents = 'auto';
            } else {
                scrollToTopBtn.style.opacity = '0';
                scrollToTopBtn.style.pointerEvents = 'none';
            }
        });

        // Remonter en haut au clic
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Initialiser le bouton de remontée
initScrollToTop();

