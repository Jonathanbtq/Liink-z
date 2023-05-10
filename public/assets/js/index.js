const btnBrgButton = document.querySelector('.button_nav_brg');
const divBrgBtn = document.querySelector('.nav-deux-ctn');
const closeBrg = document.querySelector('.burger_menu_close');

function openMenu(){
    divBrgBtn.style.display = "flex";
}

function closeMenu(){
    divBrgBtn.style.display = "none";
}