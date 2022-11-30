const $header = document.querySelector('.header');
const $sections = document.querySelectorAll('.section'); // all sections

function initFull() {
    $('.fp-main').fullpage({
        anchors: ['index', 'intro1', 'intro2', 'intro3', 'intro4', 'service1', 'service2', 'service3', 'service4', 'service5', 'advantage', 'cs', 'footer'],
        onLeave: function (index, nextIndex, direction) {
        },
        afterLoad: function (anchorLink, index) {
        }
    });
}

$(document).ready(function () {
    if ($(window).outerWidth() < 1025) {
        if ($('html').hasClass('fp-enabled')) $.fn.fullpage.destroy('all');
    } else {
        initFull();
    }
});

var resizeTimer;
$(window).on('resize', function (e) {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function (e) {
        const width = $(this).outerWidth();
        
        if (width < 1025) {
            if ($('html').hasClass('fp-enabled')) $.fn.fullpage.destroy('all');
            itemActive();
        } else {
            initFull();
        }
    }, 100);
});

// ----------------------------- End jquery ------------------------------------------

// Mobile header active with scrolling
const mobileHeaderActive = () => {
    if ( window.innerWidth <= 1024 ) { // Only Mobile
        const scrollTop = document.documentElement.scrollTop;
        
        if ( scrollTop > 0 ) {
            $header.classList.add('scrolled');
        } else {
            $header.classList.remove('scrolled');
        }
        
        for (let i = 0; i < $sections.length; i++) {
            if ( scrollTop >= $sections[i].offsetTop && scrollTop < $sections[i].offsetTop + $sections[i].clientHeight ) {
                if ( i === 0 || i === 10 ) {
                    $header.classList.add('header--white');
                } else {
                    $header.classList.remove('header--white');
                }
            }
        }
    }
}

// Scroll Active ( Only Mobile )
const itemActive = () => {
    if ( window.innerWidth < 1025 ) {
        for (let i = 0; i < $sections.length; i++) {
            if ( $sections[i].offsetTop + $sections[i].clientHeight/2 < window.scrollY + window.innerHeight ) {
                $sections[i].classList.add('active', 'fp-completely');
            }
        }
    }
}

mobileHeaderActive();
itemActive();


document.addEventListener('scroll', e => {
    mobileHeaderActive();
    itemActive();
});


// Popup
const popupOpen = () => {
    const popupDimmed = document.querySelector('.popup-dimmed');
    const popup = document.querySelector('.popup');
    
    popupDimmed.classList.add('active');
    popup.classList.add('active');
}

const popupClose = () => {
    const popupDimmed = document.querySelector('.popup-dimmed');
    const popup = document.querySelector('.popup');
    
    popupDimmed.classList.remove('active');
    popup.classList.remove('active');
}

// Bubble Text
const $bubbles = document.querySelectorAll('.bubble');

$bubbles.forEach($bubble => {
    const textArray = $bubble.innerText.split('');
    
    $bubble.innerHTML = '';
    
    for (let i = 0; i < textArray.length; i++) {
        if (textArray[i] !== ' ') {
            $bubble.innerHTML += `<span class="bubble-text">${textArray[i]}</span>`;
        } else {
            $bubble.innerHTML += ' ';
        }
    }
});


// Swiper
const serviceSwiper1 = new Swiper('.service-swiper--1 .swiper', {
    speed: 700,
    loop: true,
    allowTouchMove: true,
    slidesPerView: 1,
    spaceBetween: 25,
    navigation: {
        prevEl: '.service-swiper--1 .swiper-button--prev',
        nextEl: '.service-swiper--1 .swiper-button--next'
    },
    breakpoints: {
        1024: {
            allowTouchMove: false,
            slidesPerView: 2,
            spaceBetween: 25
        }
    }
});

const serviceSwiper2 = new Swiper('.service-swiper--2 .swiper', {
    speed: 700,
    loop: true,
    allowTouchMove: false,
    slidesPerView: 1,
    spaceBetween: 25,
    navigation: {
        prevEl: '.service-swiper--2 .swiper-button--prev',
        nextEl: '.service-swiper--2 .swiper-button--next'
    },
    breakpoints: {
        1024: {
            allowTouchMove: false,
            slidesPerView: 2,
            spaceBetween: 25
        }
    }
});

const serviceSwiper3 = new Swiper('.service-swiper--3 .swiper', {
    speed: 700,
    loop: true,
    allowTouchMove: false,
    slidesPerView: 1,
    spaceBetween: 25,
    navigation: {
        prevEl: '.service-swiper--3 .swiper-button--prev',
        nextEl: '.service-swiper--3 .swiper-button--next'
    },
    breakpoints: {
        1024: {
            allowTouchMove: false,
            slidesPerView: 2,
            spaceBetween: 25
        }
    }
});

const serviceSwiper4 = new Swiper('.service-swiper--4 .swiper', {
    speed: 700,
    loop: true,
    allowTouchMove: false,
    slidesPerView: 1,
    spaceBetween: 25,
    navigation: {
        prevEl: '.service-swiper--4 .swiper-button--prev',
        nextEl: '.service-swiper--4 .swiper-button--next'
    },
    breakpoints: {
        1024: {
            allowTouchMove: false,
            slidesPerView: 2,
            spaceBetween: 25
        }
    }
});

const serviceSwiper5 = new Swiper('.service-swiper--5 .swiper', {
    speed: 700,
    loop: true,
    allowTouchMove: false,
    slidesPerView: 1,
    spaceBetween: 25,
    navigation: {
        prevEl: '.service-swiper--5 .swiper-button--prev',
        nextEl: '.service-swiper--5 .swiper-button--next'
    },
    breakpoints: {
        1024: {
            allowTouchMove: false,
            slidesPerView: 2,
            spaceBetween: 25
        }
    }
});

const introSwiper4 = new Swiper('.intro-swiper--4 .swiper', {
    autoplay: {
        delay: 3000,
        disableOnInteraction: false
    }
});

const advantageTextSwiper = new Swiper('.advantage-text-swiper .swiper', {
    loop: true,
    autoHeight: true,
    loopedSlides: 4,
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    }
});

const advantageSwiper = new Swiper('.advantage-swiper .swiper', {
    autoplay: {
        delay: 3000,
        disableOnInteraction: false
    },
    speed: 700,
    loop: true,
    loopedSlides: 4,
    slidesPerView: 1,
    // effect: 'fade',
    // fadeEffect: {
    //     crossFade: true
    // },
    breakpoints: {
        1024: {
            slidesPerView: 2
        }
    }
});

advantageSwiper.controller.control = advantageTextSwiper;
advantageTextSwiper.controller.control = advantageSwiper;

const csSwiper = new Swiper('.cs-swiper .swiper', {
    autoplay: {
      delay: 3000
    },
    slidesPerView: 1.3,
    spaceBetween: 20
});