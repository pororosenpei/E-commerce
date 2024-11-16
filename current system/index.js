let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const totalSlides = slides.length;

document.querySelector('.hero-arrow-right').addEventListener('click', () => {
    changeSlide(1);
});

document.querySelector('.hero-arrow-left').addEventListener('click', () => {
    changeSlide(-1);
});

function changeSlide(direction) {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    slides[currentSlide].classList.add('active');
}

// Initialize first slide
slides[currentSlide].classList.add('active');


