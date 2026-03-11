/**
 * Password Validation Script
 *
 * Provides real-time validation feedback for password requirements:
 * - Minimum 12 characters
 * - At least 1 digit
 * - At least 1 uppercase letter
 * - At least 1 special character
 */

document.addEventListener('DOMContentLoaded', function() {
    // Support multiple password input fields
    const passwordInputs = [
        document.getElementById('password'),
        document.getElementById('modal_new_password')
    ].filter(input => input !== null);

    if (passwordInputs.length === 0) {
        return;
    }

    // Initialize validation for each password input
    passwordInputs.forEach(function(passwordInput) {
        initializePasswordValidation(passwordInput);
    });
});

/**
 * Initialize password validation for a specific input field
 * @param {HTMLElement} passwordInput - The password input element
 */
function initializePasswordValidation(passwordInput) {
    if (!passwordInput) {
        return;
    }

    // Get requirement indicators
    const reqLength = document.getElementById('req-length');
    const reqDigit = document.getElementById('req-digit');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqSpecial = document.getElementById('req-special');

    /**
     * Validates password and updates UI indicators
     * @param {string} password - Password to validate
     */
    function validatePassword(password) {
        // Check length (12+ characters)
        const hasLength = password.length >= 12;
        updateRequirement(reqLength, hasLength);

        // Check for at least one digit
        const hasDigit = /[0-9]/.test(password);
        updateRequirement(reqDigit, hasDigit);

        // Check for at least one uppercase letter
        const hasUppercase = /[A-Z]/.test(password);
        updateRequirement(reqUppercase, hasUppercase);

        // Check for at least one special character
        const hasSpecial = /[!@#$%^&*()\-_=+\[\]{};:'",.<>?\/\\|`~]/.test(password);
        updateRequirement(reqSpecial, hasSpecial);

        // Return overall validity
        return hasLength && hasDigit && hasUppercase && hasSpecial;
    }

    /**
     * Updates visual state of a requirement indicator
     * @param {HTMLElement} element - The requirement list item
     * @param {boolean} isValid - Whether the requirement is met
     */
    function updateRequirement(element, isValid) {
        if (!element) return;

        if (isValid) {
            element.classList.add('valid');
            element.classList.remove('invalid');
        } else {
            element.classList.add('invalid');
            element.classList.remove('valid');
        }
    }

    // Add event listener for real-time validation
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    // Validate on form submit
    const form = passwordInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;

            // Vérifier tous les critères
            const missing = [];

            if (password.length < 12) {
                missing.push('au moins 12 caractères');
            }
            if (!/[0-9]/.test(password)) {
                missing.push('au moins 1 chiffre');
            }
            if (!/[A-Z]/.test(password)) {
                missing.push('au moins 1 lettre majuscule');
            }
            if (!/[!@#$%^&*()\-_=+\[\]{};:'",.<>?\/\\|`~]/.test(password)) {
                missing.push('au moins 1 caractère spécial (!@#$%^&*...)');
            }

            if (missing.length > 0) {
                e.preventDefault();
                const missingList = missing.join(', ');
                alert(`Le mot de passe doit contenir : ${missingList}.`);
                return false;
            }

            // Check password confirmation if exists
            const confirmInput = document.getElementById('confirm-password') || document.getElementById('confirm_password');
            if (confirmInput && confirmInput.value !== password) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
        });
    }
}

