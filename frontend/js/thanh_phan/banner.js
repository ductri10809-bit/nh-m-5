/**
 * banner.js - Carousel 5s
 */
function khoiTaoBanner() {
  const slides = document.querySelectorAll('.hero-slide');
  const dotsWrap = document.getElementById('hero-dots');
  if (!slides.length) return;

  let current = 0;
  const duration = 5000;
  let timer;

  slides.forEach((_, i) => {
    if (!dotsWrap) return;
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'hero-dot' + (i === 0 ? ' is-active' : '');
    dot.addEventListener('click', () => goTo(i));
    dotsWrap.appendChild(dot);
  });

  const dots = () => document.querySelectorAll('.hero-dot');

  function goTo(index) {
    slides[current].classList.remove('is-active');
    dots()[current]?.classList.remove('is-active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('is-active');
    dots()[current]?.classList.add('is-active');
    resetTimer();
  }

  function next() {
    goTo(current + 1);
  }

  function resetTimer() {
    clearInterval(timer);
    timer = setInterval(next, duration);
  }

  resetTimer();
}

document.addEventListener('DOMContentLoaded', khoiTaoBanner);
document.addEventListener('componentLoaded', (e) => {
  if (e.detail?.selector === '#banner') khoiTaoBanner();
});
