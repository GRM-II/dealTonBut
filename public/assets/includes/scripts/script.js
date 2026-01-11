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
    const scrollIcon = document.getElementById('scroll-icon');

    if (tradeIcon) {
        tradeIcon.src = isDark ? '/public/assets/img/Trade_Night.svg' : '/public/assets/img/Trade_Day.svg';
    }
    if (marketIcon) {
        marketIcon.src = isDark ? '/public/assets/img/Market_Night.svg' : '/public/assets/img/Market_Day.svg';
    }
    if (homeIcon) {
        homeIcon.src = isDark ? '/public/assets/img/Home_Night.svg' : '/public/assets/img/Home_Day.svg';
    }
    if (scrollIcon) {
        scrollIcon.src = isDark ? '/public/assets/img/Black_Arrow.svg' : '/public/assets/img/Blue_Arrow.svg';
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

    // Redessiner le graphique de la page de profil, sinon le texte ne change pas de couleur avec le thème
    const chartElement = document.getElementById('barchart_values');
    if (chartElement && typeof google !== 'undefined' && google.charts) {
        initGradesChartFromData();
    }
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

    if (document.getElementById('delete-account-btn')) {
        initProfilePage();

        const chartElement = document.getElementById('barchart_values');
        if (chartElement) {
            if (typeof google !== 'undefined' && google.charts) {
                initGradesChartFromData();
            } else {
                setTimeout(initGradesChartFromData, 500);
            }
        }
    } else if (document.getElementById('open-modal-btn')) {
        initMarketplace();
    } else if (document.getElementById('purchase-offer-btn')) {
        initTradeplace();
    } else if (document.getElementById('register-form')) {
        const dbUnavailableElem = document.querySelector('.db-unavailable-message');
        const dbUnavailable = dbUnavailableElem !== null;
        const dbMessage = dbUnavailable ? dbUnavailableElem.textContent.trim() : '';
        initRegisterForm(dbUnavailable, dbMessage);
    }

    initForgotPasswordModal();
});

function initForgotPasswordModal() {
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    const forgotPasswordModal = document.getElementById('forgot-password-modal');
    const closeBtn = forgotPasswordModal ? forgotPasswordModal.querySelector('.close') : null;

    if (forgotPasswordLink && forgotPasswordModal) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();

            forgotPasswordModal.style.display = 'flex';

            void forgotPasswordModal.offsetHeight;

            forgotPasswordModal.style.opacity = '1';
            const modalContent = forgotPasswordModal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                forgotPasswordModal.style.opacity = '0';
                const modalContent = forgotPasswordModal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.transform = 'scale(0.9)';
                }
                setTimeout(function() {
                    forgotPasswordModal.style.display = 'none';
                }, 400);
            });
        }

        forgotPasswordModal.addEventListener('click', function(e) {
            if (e.target === forgotPasswordModal) {
                forgotPasswordModal.style.opacity = '0';
                const modalContent = forgotPasswordModal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.transform = 'scale(0.9)';
                }
                setTimeout(function() {
                    forgotPasswordModal.style.display = 'none';
                }, 400);
            }
        });
    }

    const forgotPasswordForm = document.getElementById('forgot-password-form');
    const submitButton = document.getElementById('submit-forgot-password');
    const loadingIndicator = document.getElementById('loading-indicator');

    if (forgotPasswordForm && submitButton) {
        forgotPasswordForm.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.style.opacity = '0.6';
            submitButton.style.cursor = 'not-allowed';
            submitButton.textContent = 'Envoi en cours...';
            if (loadingIndicator) {
                loadingIndicator.style.display = 'block';
            }
        });
    }
}

function initMarketplace() {

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

    const categoryCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
    const allCheckbox = document.querySelector('.filter-option input[value="all"]');

    if (categoryCheckboxes.length > 0) {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.value === 'all' && this.checked) {
                    categoryCheckboxes.forEach(cb => {
                        if (cb.value !== 'all') cb.checked = false;
                    });
                } else if (this.value !== 'all' && this.checked) {
                    if (allCheckbox) allCheckbox.checked = false;
                }

                const selectedCategories = Array.from(categoryCheckboxes)
                    .filter(cb => cb.checked && cb.value !== 'all')
                    .map(cb => cb.value);

                document.querySelectorAll('.category-section').forEach(section => {
                    const categoryName = section.querySelector('.category-name').textContent;

                    if (allCheckbox && allCheckbox.checked || selectedCategories.length === 0) {
                        section.style.display = 'block';
                    } else if (selectedCategories.includes(categoryName)) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
            });
        });
    }
}

function initTradeplace() {

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            void modal.offsetHeight;
            modal.style.opacity = '1';
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.opacity = '0';
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.9)';
            }
            setTimeout(function() {
                modal.style.display = 'none';
            }, 400);
        }
    }

    const purchaseOfferBtn = document.getElementById('purchase-offer-btn');
    const purchaseModal = document.getElementById('purchase-modal');
    const cancelPurchaseBtn = document.getElementById('cancel-purchase-btn');

    if (purchaseOfferBtn && purchaseModal) {
        purchaseOfferBtn.addEventListener('click', function() {
            openModal('purchase-modal');
        });

        if (cancelPurchaseBtn) {
            cancelPurchaseBtn.addEventListener('click', function() {
                closeModal('purchase-modal');
            });
        }

        purchaseModal.addEventListener('click', function(e) {
            if (e.target === purchaseModal) {
                closeModal('purchase-modal');
            }
        });
    }
}

function initProfilePage() {
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            void modal.offsetHeight;
            modal.style.opacity = '1';
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.opacity = '0';
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.9)';
            }
            setTimeout(function() {
                modal.style.display = 'none';
            }, 400);
        }
    }

    const editUsernameBtn = document.getElementById('edit-username-btn');
    if (editUsernameBtn) {
        editUsernameBtn.addEventListener('click', function() {
            openModal('username-modal');
        });
    }

    const editEmailBtn = document.getElementById('edit-email-btn');
    if (editEmailBtn) {
        editEmailBtn.addEventListener('click', function() {
            openModal('email-modal');
        });
    }

    const editPasswordBtn = document.getElementById('edit-password-btn');
    if (editPasswordBtn) {
        editPasswordBtn.addEventListener('click', function() {
            openModal('password-modal');
        });
    }

    const editAllGradesBtn = document.getElementById('edit-all-grades-btn');
    if (editAllGradesBtn) {
        editAllGradesBtn.addEventListener('click', function() {
            openModal('all-grades-modal');
        });
    }

    const cancelButtons = document.querySelectorAll('.cancel-modal');
    cancelButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            closeModal(modalId);
        });
    });

    const profileModals = document.querySelectorAll('.profile-modal');
    profileModals.forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const deleteModal = document.getElementById('delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    if (deleteAccountBtn && deleteModal) {
        deleteAccountBtn.addEventListener('click', function() {
            openModal('delete-modal');
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', function() {
                closeModal('delete-modal');
            });
        }

        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeModal('delete-modal');
            }
        });
    }
}

function initScrollToTop() {
    const scrollToTopBtn = document.getElementById('scroll-to-top-btn');

    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                scrollToTopBtn.style.opacity = '1';
                scrollToTopBtn.style.pointerEvents = 'auto';
            } else {
                scrollToTopBtn.style.opacity = '0';
                scrollToTopBtn.style.pointerEvents = 'none';
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

initScrollToTop();

function initGradesChartFromData() {
    const chartElement = document.getElementById('barchart_values');
    if (!chartElement) return;

    const gradesData = {
        maths: chartElement.getAttribute('data-maths') || '0',
        programmation: chartElement.getAttribute('data-programmation') || '0',
        network: chartElement.getAttribute('data-network') || '0',
        db: chartElement.getAttribute('data-db') || '0',
        other: chartElement.getAttribute('data-other') || '0'
    };

    initGradesChart(gradesData);
}

function initGradesChart(gradesData) {
    if (typeof google === 'undefined' || !google.charts) {
        console.error('Google Charts n\'est pas chargé');
        return;
    }

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(function() {
        drawChart(gradesData);
    });
}

function getCSSVariable(variableName) {
    return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
}

function drawChart(gradesData) {
    const isDark = document.body.classList.contains('dark-theme');

    // Récupération des couleurs depuis les variables CSS
    const primaryColor = getCSSVariable('--primary-color');
    const textColor = isDark ? getCSSVariable('--text-dark') : getCSSVariable('--text-light');
    const gridColor = isDark ? getCSSVariable('--chart-grid-dark') : getCSSVariable('--chart-grid-light');

    const data = google.visualization.arrayToDataTable([
        ["Moyennes", "Moyenne", { role: "style" }],
        ["Maths", parseFloat(gradesData.maths) || 0, `color: ${primaryColor}`],
        ["Programmation", parseFloat(gradesData.programmation) || 0, `color: ${primaryColor}`],
        ["Réseau", parseFloat(gradesData.network) || 0, `color: ${primaryColor}`],
        ["BD", parseFloat(gradesData.db) || 0, `color: ${primaryColor}`],
        ["Autres", parseFloat(gradesData.other) || 0, `color: ${primaryColor}`]
    ]);

    const view = new google.visualization.DataView(data);
    view.setColumns([0, 1,
        {
            calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"
        },
        2
    ]);

    const chartElement = document.getElementById("barchart_values");
    const containerWidth = chartElement ? chartElement.parentElement.clientWidth : 600;
    const isMobile = window.innerWidth < 768;
    const chartHeight = isMobile ? Math.min(250, window.innerHeight * 0.3) : Math.min(400, window.innerHeight * 0.5);

    const options = {
        title: "Aperçu visuel de vos moyennes",
        width: containerWidth - 30,
        height: chartHeight,
        backgroundColor: 'transparent',
        bar: {groupWidth: isMobile ? "50%" : "60%"},
        legend: { position: "none" },
        titleTextStyle: {
            color: textColor,
            fontSize: isMobile ? 14 : 16,
            bold: true
        },
        hAxis: {
            title: 'Points / 20',
            titleTextStyle: {
                color: textColor,
                fontSize: isMobile ? 11 : 13
            },
            textStyle: {
                color: textColor,
                fontSize: isMobile ? 10 : 12
            },
            viewWindow: {
                max: 20,
                min: 0
            },
            gridlines: {
                color: gridColor
            }
        },
        vAxis: {
            textStyle: {
                color: textColor,
                fontSize: isMobile ? 10 : 12
            },
            gridlines: {
                color: gridColor
            }
        },
        chartArea: {
            width: '70%',
            height: '70%'
        }
    };

    const chart = new google.visualization.BarChart(chartElement);
    chart.draw(view, options);

    window.addEventListener('resize', function() {
        const newWidth = chartElement.parentElement.clientWidth;
        const newIsMobile = window.innerWidth < 768;
        const newHeight = newIsMobile ? Math.min(250, window.innerHeight * 0.3) : Math.min(400, window.innerHeight * 0.5);
        options.width = newWidth - 30;
        options.height = newHeight;
        options.bar.groupWidth = newIsMobile ? "50%" : "60%";
        options.titleTextStyle.fontSize = newIsMobile ? 14 : 16;
        chart.draw(view, options);
    });
}
