/**
 * Yumland - Phase 3
 * Gestion du changement de charte graphique (Mode Clair / Mode Accessible)
 * Utilisation des cookies pour la persistance du choix
 */

document.addEventListener('DOMContentLoaded', function () {
    const themeBtn = document.getElementById('theme-switch');
    const themeLink = document.getElementById('dynamic-theme');

    // Valeurs autorisées (cohérence avec la validation PHP)
    const THEME_DEFAULT = 'css/global.css';
    const THEME_ACCESSIBLE = 'css/accessible.css';

    if (!themeBtn || !themeLink) return;

    themeBtn.addEventListener('click', function () {
        // On lit l'attribut href et on ne garde que la partie chemin
        // pour éviter le problème des URL absolues (http://localhost/css/...)
        const currentHref = themeLink.getAttribute('href');
        const isAccessible = currentHref.endsWith('accessible.css');

        const newPath = isAccessible ? THEME_DEFAULT : THEME_ACCESSIBLE;
        const btnText = isAccessible ? '🌓 Mode Accessible' : '☀️ Mode Clair';

        // Changement dynamique du CSS sans rechargement de page
        themeLink.setAttribute('href', newPath);

        // Mise à jour du texte du bouton
        themeBtn.textContent = btnText;

        // Sauvegarde dans le cookie (expiration 30 jours)
        const d = new Date();
        d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
        document.cookie = "theme_choice=" + newPath
            + ";expires=" + d.toUTCString()
            + ";path=/";

        console.log("Thème changé : " + newPath);
    });
});