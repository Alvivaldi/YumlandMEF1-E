
document.addEventListener('DOMContentLoaded', function () {
    const themeBtn = document.getElementById('theme-switch');
    const themeLink = document.getElementById('dynamic-theme');


    const THEME_DEFAULT = 'css/global.css';
    const THEME_ACCESSIBLE = 'css/accessible.css';

    if (!themeBtn || !themeLink) return;

    themeBtn.addEventListener('click', function () {

        const currentHref = themeLink.getAttribute('href');
        const isAccessible = currentHref.endsWith('accessible.css');

        const newPath = isAccessible ? THEME_DEFAULT : THEME_ACCESSIBLE;
        const btnText = isAccessible ? '🌓 Mode Accessible' : '☀️ Mode Clair';


        themeLink.setAttribute('href', newPath);


        themeBtn.textContent = btnText;


        const d = new Date();
        d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
        document.cookie = "theme_choice=" + newPath
            + ";expires=" + d.toUTCString()
            + ";path=/";

        console.log("Thème changé : " + newPath);
    });
});