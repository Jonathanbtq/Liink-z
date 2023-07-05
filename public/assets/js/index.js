const burger = document.querySelector('.button_nav_brg');
const divActive = document.querySelector('.nav-deux-ctn');

burger.addEventListener('click', () => {
    burger.classList.toggle('burger_active');
    divActive.classList.toggle('nav_deux_active')
})

// const slidingDiv = document.querySelectorAll('.idx_why_ctn_card');

// window.addEventListener('scroll', () =>{
//     const {scrollTop, clientHeight} = document.documentElement;

//     const topElemenetToTopViewport = slidingDiv.getBoundingClientRect().top;

//     if(scroll > (scrollTop + topElemenetToTopViewport).toFixed() - clientHeight * 0.80){
//         slidingDiv.classList.add('active')
//     }
// })

// var images = document.querySelectorAll('.right_idx_head img');
// var currentIndex = 0;

// function showNextImage() {
//   images[currentIndex].style.transform = 'scale(1.2)';
//   currentIndex = (currentIndex + 1) % images.length;
//   images[currentIndex].style.opacity = '0.8';
//   images[currentIndex].style.transform = 'scale(1)';

//   if (currentIndex === 1) {
//     images[0].classList.add('img_idx_deux');
//     images[1].classList.remove('img_idx_deux');
//     images[1].classList.add('img_idx_one');
//     images[0].classList.remove('img_idx_one');
//   }
// }

// setInterval(showNextImage, 3000);