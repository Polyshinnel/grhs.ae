import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const heroVideo = document.querySelector('[data-home-hero-video]');

if (heroVideo) {
    if (reducedMotion.matches) {
        heroVideo.removeAttribute('autoplay');
        heroVideo.pause();
    } else {
        heroVideo.play().catch(() => {});
    }

    reducedMotion.addEventListener('change', (event) => {
        if (event.matches) {
            heroVideo.pause();
            heroVideo.removeAttribute('autoplay');
        } else {
            heroVideo.setAttribute('autoplay', '');
            heroVideo.play().catch(() => {});
        }
    });
}

const partnerSliders = [];

const bestsellersSliders = [];

const brandSliders = [];

document.querySelectorAll('[data-bestsellers-slider]').forEach((element) => {
    bestsellersSliders.push(new Swiper(element, {
        modules: [Autoplay],
        slidesPerView: 1.08,
        slidesPerGroup: 1,
        spaceBetween: 16,
        speed: reducedMotion.matches ? 0 : 650,
        loop: true,
        grabCursor: true,
        simulateTouch: true,
        watchOverflow: true,
        autoplay: reducedMotion.matches ? false : {
            delay: 3500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1.5,
                spaceBetween: 20,
            },
            800: {
                slidesPerView: 2,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 24,
            },
        },
    }));
});

document.querySelectorAll('[data-partners-slider]').forEach((element) => {
    const slideCount = element.querySelectorAll('.swiper-slide').length;

    partnerSliders.push(new Swiper(element, {
        modules: [Autoplay],
        slidesPerView: 3,
        slidesPerGroup: 1,
        spaceBetween: 4,
        speed: 650,
        loop: slideCount > 10,
        autoplay: reducedMotion.matches ? false : {
            delay: 1000,
            disableOnInteraction: false,
            pauseOnMouseEnter: false,
        },
        breakpoints: {
            640: {
                slidesPerView: 5,
                slidesPerGroup: 1,
                spaceBetween: 8,
            },
            1024: {
                slidesPerView: 8,
                slidesPerGroup: 1,
                spaceBetween: 12,
            },
            1440: {
                slidesPerView: 10,
                spaceBetween: 16,
            },
        },
    }));
});

document.querySelectorAll('[data-brands-slider]').forEach((element) => {
    const slideCount = element.querySelectorAll('.swiper-slide').length;

    brandSliders.push(new Swiper(element, {
        modules: [Autoplay],
        slidesPerView: 2,
        slidesPerGroup: 1,
        spaceBetween: 8,
        speed: reducedMotion.matches ? 0 : 650,
        loop: true,
        watchOverflow: true,
        autoplay: reducedMotion.matches ? false : {
            delay: 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 4,
                spaceBetween: 12,
            },
            1024: {
                slidesPerView: 6,
                spaceBetween: 16,
            },
        },
    }));
});

reducedMotion.addEventListener('change', (event) => {
    bestsellersSliders.forEach((slider) => {
        if (event.matches) {
            slider.params.speed = 0;
            slider.autoplay.stop();
        } else {
            slider.params.speed = 650;
            slider.autoplay.start();
        }
    });

    partnerSliders.forEach((slider) => {
        if (event.matches) {
            slider.autoplay.stop();
        } else {
            slider.autoplay.start();
        }
    });

    brandSliders.forEach((slider) => {
        if (event.matches) {
            slider.params.speed = 0;
            slider.autoplay.stop();
        } else {
            slider.params.speed = 650;
            slider.autoplay.start();
        }
    });
});
