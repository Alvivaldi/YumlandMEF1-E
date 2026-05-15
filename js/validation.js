document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // UTILITAIRES
    // =========================================================
    function showError(inputEl, msgId, message) {
        const span = document.getElementById(msgId);
        if (span) span.textContent = message;
        if (inputEl) inputEl.classList.add('invalid');
    }

    function clearError(inputEl, msgId) {
        const span = document.getElementById(msgId);
        if (span) span.textContent = "";
        if (inputEl) inputEl.classList.remove('invalid');
    }

    function clearAll() {
        document.querySelectorAll('.error-msg').forEach(s => s.textContent = "");
        document.querySelectorAll('input').forEach(i => i.classList.remove('invalid'));
    }

    // =========================================================
    // AFFICHER / CACHER LES MOTS DE PASSE (icône œil)
    // =========================================================
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // =========================================================
    // VALIDATION EN TEMPS RÉEL (au fur et à mesure de la saisie)
    // =========================================================
    const emailInput = document.querySelector('input[name="email"]');
    const telInput = document.querySelector('input[name="telephone"]');
    const passInput = document.getElementById('reg-password');
    const nomInput = document.querySelector('input[name="nom"]');
    const prenomInput = document.querySelector('input[name="prenom"]');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const telRegex = /^(\+33|0)[1-9](\s?\d{2}){4}$/;

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            if (emailInput.value && !emailRegex.test(emailInput.value)) {
                showError(emailInput, 'err-email', "Format d'email invalide (ex: nom@domaine.fr)");
            } else {
                clearError(emailInput, 'err-email');
            }
        });
    }

    if (telInput) {
        telInput.addEventListener('input', () => {
            const val = telInput.value.trim();
            if (val && !telRegex.test(val)) {
                showError(telInput, 'err-telephone', "Numéro invalide (ex: 06 12 34 56 78)");
            } else {
                clearError(telInput, 'err-telephone');
            }
        });
    }

    if (passInput) {
        passInput.addEventListener('input', () => {
            const v = passInput.value;
            if (v.length > 0 && v.length < 8) {
                showError(passInput, 'err-password', "Au moins 8 caractères requis");
            } else if (v.length > 20) {
                showError(passInput, 'err-password', "Maximum 20 caractères");
            } else {
                clearError(passInput, 'err-password');
            }
            updateCounter('count-password', v.length, 20);
        });
    }

    if (prenomInput) {
        prenomInput.addEventListener('input', () => {
            updateCounter('count-nom', prenomInput.value.length, 30);
        });
    }

    function updateCounter(spanId, current, max) {
        const span = document.getElementById(spanId);
        if (!span) return;
        span.textContent = current;
        const counter = span.closest('.char-counter');
        if (counter) counter.classList.toggle('limit-reached', current >= max);
    }

    // =========================================================
    // VALIDATION À LA SOUMISSION (bloque si invalide)
    // =========================================================
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', (event) => {
        clearAll();
        let isValid = true;

        // --- Nom (lettres uniquement, 2–30 chars) ---
        if (nomInput) {
            const nomRegex = /^[A-Za-zÀ-ÿ\s\-]{2,30}$/;
            if (!nomRegex.test(nomInput.value.trim())) {
                showError(nomInput, 'err-nom', "Nom invalide (lettres uniquement, 2 à 30 caractères)");
                isValid = false;
            }
        }

        // --- Prénom (lettres uniquement, 2–30 chars) ---
        if (prenomInput) {
            const prenomRegex = /^[A-Za-zÀ-ÿ\s\-]{2,30}$/;
            if (!prenomRegex.test(prenomInput.value.trim())) {
                showError(prenomInput, 'err-prenom', "Prénom invalide (lettres uniquement, 2 à 30 caractères)");
                isValid = false;
            }
        }

        // --- Email ---
        if (emailInput) {
            if (!emailRegex.test(emailInput.value.trim())) {
                showError(emailInput, 'err-email', "Format d'email invalide (ex: nom@domaine.fr)");
                isValid = false;
            }
        }

        // --- Téléphone ---
        if (telInput) {
            if (!telRegex.test(telInput.value.trim())) {
                showError(telInput, 'err-telephone', "Numéro invalide (ex: 06 12 34 56 78 ou +33 6 12 34 56 78)");
                isValid = false;
            }
        }

        // --- Mot de passe ---
        if (passInput) {
            if (passInput.value.length < 8) {
                showError(passInput, 'err-password', "Le mot de passe doit faire au moins 8 caractères");
                isValid = false;
            } else if (passInput.value.length > 20) {
                showError(passInput, 'err-password', "Le mot de passe ne doit pas dépasser 20 caractères");
                isValid = false;
            }
        }

        if (!isValid) event.preventDefault();
    });
});