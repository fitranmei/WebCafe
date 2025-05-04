document.addEventListener('DOMContentLoaded', function () {
    const themeIcon = document.querySelector('.tema-icon i');
    const htmlElement = document.documentElement; 

    if (!themeIcon) {
        console.error("Theme icon not found");
        return;
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setThemeCookie(theme) {
        document.cookie = `theme=${theme}; path=/; max-age=31536000`; 
    }

    function loadTheme() {
        const savedTheme = getCookie('theme');
        if (savedTheme) {
            htmlElement.setAttribute('data-theme', savedTheme);
        } else {
            htmlElement.setAttribute('data-theme', 'dark'); 
        }
    }

    function enableLightMode() {
        htmlElement.setAttribute('data-theme', 'light'); 
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        setThemeCookie('light'); 
    }

    function enableDarkMode() {
        htmlElement.setAttribute('data-theme', 'dark'); 
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        setThemeCookie('dark'); 
    }

    themeIcon.addEventListener('click', function () {
        if (htmlElement.getAttribute('data-theme') === 'dark') {
            enableLightMode();
        } else {
            enableDarkMode();
        }
    });

    loadTheme();
});
