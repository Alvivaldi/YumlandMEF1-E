document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('reg-password');
    const togglePassword = document.getElementById('togglePassword');

    // --- 1. Afficher/Cacher le mot de passe ---
    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash'); // Change l'icône
        });
    }

    // --- 2. Validation au moment de l'envoi ---
    form.addEventListener('submit', (event) => {
        let isValid = true;

        // Reset des erreurs
        document.querySelectorAll('.error-msg').forEach(span => span.textContent = "");
        document.querySelectorAll('input').forEach(input => input.classList.remove('invalid'));

        // Vérification Email (Regex)
        const email = document.getElementById('reg-email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            document.getElementById('err-email').textContent = "Format d'email invalide.";
            email.classList.add('invalid');
            isValid = false;
        }

        // Vérification Téléphone (10 chiffres)
        const tel = document.getElementsByName('telephone')[0];
        if (tel.value.length < 10 || isNaN(tel.value)) {
            document.getElementById('err-telephone').textContent = "Le numéro doit contenir 10 chiffres.";
            tel.classList.add('invalid');
            isValid = false;
        }

        // Vérification Mot de passe (ex: min 6 caractères)
        if (passwordInput.value.length < 6) {
            document.getElementById('err-password').textContent = "Le mot de passe doit faire au moins 6 caractères.";
            passwordInput.classList.add('invalid');
            isValid = false;
        }

        // SI PAS VALIDE : On bloque l'envoi
        if (!isValid) {
            event.preventDefault();
        }
    });
});