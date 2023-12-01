document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const rightNav = document.querySelector('.right-nav');
    const rightNavTop = document.querySelector('.right-nav-top');

    rightNav.classList.add('show');

    navToggle.addEventListener('click', function () {
        rightNav.classList.toggle('show');
    });

    rightNavTop.addEventListener('click', function () {
        rightNav.classList.remove('show');
    });
});
