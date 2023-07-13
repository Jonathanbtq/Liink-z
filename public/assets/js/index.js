const burger = document.querySelector('.button_nav_brg');
const divActive = document.querySelector('.nav-deux-ctn');

burger.addEventListener('click', () => {
    burger.classList.toggle('burger_active');
    divActive.classList.toggle('nav_deux_active')
})

document.addEventListener('DOMContentLoaded', function() {
    var slider = document.querySelector('.ctn_slider_idx');
    var sliderItems = document.querySelectorAll('.ctn_slider_idx img');

    var slideWidth = sliderItems[0].clientWidth;
    var slideCount = sliderItems.length;
    var currentIndex = 0;

    function slideTo(index) {
        if (index < 0 || index >= slideCount) {
            return;
        }

        slider.style.transform = 'translateX(' + (-slideWidth * index) + 'px)';
        currentIndex = index;
    }

    function slideNext() {
        var newIndex = currentIndex + 3;
        if (newIndex >= slideCount) {
            newIndex = 0;
        }
        slideTo(newIndex);
    }

    setInterval(slideNext, 4000);
});