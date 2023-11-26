document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const rightNav = document.querySelector('.right-nav');
    const rightNavTop = document.querySelector('.right-nav-top');

    navToggle.addEventListener('click', function () {
        rightNav.classList.toggle('show');
    });

    // Close the right navigation when clicking on an element inside .right-nav-top
    rightNavTop.addEventListener('click', function () {
        rightNav.classList.remove('show');
    });
});
