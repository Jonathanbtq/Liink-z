const burger = document.querySelector('.button_nav_brg');
const divActive = document.querySelector('.nav-deux-ctn');

burger.addEventListener('click', () => {
    burger.classList.toggle('burger_active');
    divActive.classList.toggle('nav_deux_active')
})