const slides = [
  { img: 'img/best/280b5395026ccd8b832982b7c2923e21.jpg', word: '..change' },
  { img: 'img/best/419c2ba78dc2590c16ebc4175e4f2fa5.jpg', word: '..living your dreams' },
  { img: 'img/best/183d13d9956a8f13db4109f53eb581af.jpg', word: '..learning' },
  { img: 'img/best/c9261947764e6c603e555a3c79f8e664.jpg', word: '..doing what you love' }
];
let current = 0;
const imgEl = document.getElementById('slideshow-img');
const wordEl = document.getElementById('slideshow-word');

function showSlide(idx) {
  imgEl.classList.add('opacity-0');
  setTimeout(() => {
    imgEl.src = slides[idx].img;
    wordEl.textContent = slides[idx].word;
    imgEl.classList.remove('opacity-0');
  }, 500);
}

setInterval(() => {
  current = (current + 1) % slides.length;
  showSlide(current);
}, 3000);