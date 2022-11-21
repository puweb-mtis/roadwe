function initFull() {
    let fullpagePrevIndex;
    $('#fullpage').fullpage({
        menu: '#fullpage_menu',
        anchors: ['intro', 'technology', 'promotion', 'rnd', 'footer'],
        onLeave: function (index, nextIndex, direction) {
            fullpagePrevIndex = index - 1;
            
            const $animateItems = $(this).find('.animate__animated.fullpage__animated');
            
            $animateItems.removeClass('animate__fadeInUp');
            
            if (nextIndex !== 1) {
                $('.header').removeClass('header--white');
            } else {
                $('.header').addClass('header--white');
            }
        },
        afterLoad: function (anchorLink, index) {
            const $animateItems = $(this).find('.animate__animated.fullpage__animated');
            $animateItems.addClass('animate__fadeInUp');
            
            techSwiper.autoplay.stop();
            
            if (fullpagePrevIndex !== undefined) {
                switch ((index - 1)) {
                    case 0:
                        introSwiper.autoplay.start();
                        introSwiper.slideTo(0);
                        introSwiper.el.querySelector('.swiper-progressbar').classList.add('active');
                        break;
                    case 1:
                        console.log('tech');
                        techSwiper.autoplay.start();
                        techSwiper.slideTo(0);
                        techSwiper.slides[0].querySelector('.swiper-progressbar').classList.add('active');
                        break;
                    default:
                        break;
                }
            }
            
            switch (fullpagePrevIndex) {
                case 0:
                    introSwiper.autoplay.stop();
                    introSwiper.slideTo(0);
                    introSwiper.el.querySelector('.swiper-progressbar').classList.remove('active');
                    break;
                case 1:
                    techSwiper.autoplay.stop();
                    techSwiper.slideTo(0);
                    techSwiper.slides[0].querySelector('.swiper-progressbar').classList.remove('active');
                    break;
                default:
                    break;
            }
        }
    });
}

$(document).ready(function () {
    if ($(window).outerWidth() < 1025) {
        if ($('html').hasClass('fp-enabled')) {
            $.fn.fullpage.destroy('all');
        }
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
            $.fn.fullpage.destroy('all');
        } else {
            initFull();
        }
    }, 100);
});

const introSwiper = new Swiper('.intro-swiper', {
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    },
    speed: 1000,
    spaceBetween: 0,
    autoplay: {
        delay: 6000,
        disableOnInteraction: false
    },
    navigation: {
        prevEl: '.intro-swiper .swiper-button--prev',
        nextEl: '.intro-swiper .swiper-button--next',
    },
    pagination: {
        el: '.intro-swiper .swiper-pagination--custom',
        type: 'bullets',
        clickable: true
    }
});

introSwiper.on('slideChange', swiper => {
    const $slides = swiper.slides;
    const activeIndex = swiper.activeIndex;
    const letterWrappers = $slides[activeIndex].querySelectorAll('[data-letters-wrapper]');
    
    for (let i = 0; i < letterWrappers.length; i++) {
        const lettersWrapper = letterWrappers[i];
        const letterArray = lettersWrapper.textContent.split(/\s/g);
        
        lettersWrapper.innerHTML = '';
        
        for (let j = 0; j < letterArray.length; j++) {
            lettersWrapper.innerHTML += `<span class="letter">${letterArray[j]}</span>&nbsp;`;
        }
        
        anime.timeline({loop: false}).add({
            targets: lettersWrapper.querySelectorAll('.letter'),
            translateY: ["1.5em", 0],
            duration: 1000,
            easing: "easeOutCubic",
            delay: (el, i) => 200 * i
        });
    }
});

/* TECH */
const techSwiper = new Swiper('.tech-swiper', {
    autoplay: {
        delay: 4000,
        disableOnInteraction: false
    },
    speed: 500,
    slidesPerView: 1,
    spaceBetween: 0,
    navigation: {
        prevEl: '.tech .swiper-button--arrow-left',
        nextEl: '.tech .swiper-button--arrow-right',
    },
    pagination: {
        el: '.tech .swiper-pagination--custom',
        clickable: true
    },
    breakpoints: {
        1024: {
            slidesPerView: 1.2,
            spaceBetween: 50
        },
        1400: {
            slidesPerView: 1.5,
            spaceBetween: 100
        },
        1600: {
            slidesPerView: 1,
            spaceBetween: 100
        }
    }
});

document.querySelector('.tech .swiper-button--prev').addEventListener('click', e => {
    techSwiper.slidePrev();
});

document.querySelector('.tech .swiper-button--next').addEventListener('click', e => {
    techSwiper.slideNext();
});

/* PROMO */
const promoSwipers = [];
const promoSwiperElements = document.querySelectorAll('.promo .swiper');

for (let i = 0; i < promoSwiperElements.length; i++) {
    promoSwipers[i] = new Swiper(promoSwiperElements[i], {
        slidesPerView: 1,
        spaceBetween: 60,
        pagination: {
            el: promoSwiperElements[i].querySelector('.swiper-pagination--custom'),
            clickable: true
        },
        navigation: {
            prevEl: promoSwiperElements[i].querySelector('.swiper-button--prev'),
            nextEl: promoSwiperElements[i].querySelector('.swiper-button--next')
        },
        breakpoints: {
            1024: {
                slidesPerView: 4
            }
        }
    });
    
    swiperPaginationNumber(promoSwipers[i]);
    
    promoSwiperElements[i].querySelector('.swiper-button--arrow-left').addEventListener('click', e => {
        promoSwipers[i].slidePrev();
    });
    
    promoSwiperElements[i].querySelector('.swiper-button--arrow-right').addEventListener('click', e => {
        promoSwipers[i].slideNext();
    });
}

/* R&D */
const rndTitleSlider = document.querySelector('.rnd-title-slider')
const rndTitleSlides = document.querySelectorAll('.rnd-title-slider .rnd-title-slide');
const slideWidth = rndTitleSlides[0].clientWidth;
const slideHeight = rndTitleSlides[0].clientHeight;
const rndSwiperTop = new Swiper('.rnd-top', {
    allowTouchMove: true,
    pagination: {
        el: '.rnd-title-box .swiper-pagination--bullet .swiper-pagination--custom',
        clickable: true
    },
    navigation: {
        prevEl: '.rnd-title-box .swiper-pagination--bullet .swiper-button--prev',
        nextEl: '.rnd-title-box .swiper-pagination--bullet .swiper-button--next',
    },
    breakpoints: {
        1024: {
            allowTouchMove: false
        }
    }
});

rndTitleSlider.style.position = 'relative';
rndTitleSlider.classList.add('initialized');
rndTitleSlider.style.width = slideWidth + 'px';
rndTitleSlider.style.height = slideHeight + 'px';

window.addEventListener('resize', e => {
    const slideWidth = rndTitleSlides[0].clientWidth;
    const slideHeight = rndTitleSlides[0].clientHeight;
    
    rndTitleSlider.style.width = slideWidth + 'px';
    rndTitleSlider.style.height = slideHeight + 'px';
});

rndSwiperTop.on('slideChange', swiper => {
    rndTitleSlides[swiper.activeIndex].classList.add('active');
    rndTitleSlides[swiper.previousIndex].classList.remove('active');
});

const rndSwiperBottom = new Swiper('.rnd-bottom', {
    allowTouchMove: false
});

rndSwiperTop.on('slideNextTransitionEnd', swiper => {
    rndSwiperBottom.slideNext();
});

rndSwiperTop.on('slidePrevTransitionEnd', swiper => {
    rndSwiperBottom.slidePrev();
});

// rndSwiperTop.controller.control = rndSwiperBottom;
// rndSwiperBottom.controller.control = rndSwiperTop;

document.querySelector('.rnd-swiper .swiper-button--arrow-left').addEventListener('click', e => {
    rndSwiperTop.slidePrev();
});

document.querySelector('.rnd-swiper .swiper-button--arrow-right').addEventListener('click', e => {
    rndSwiperTop.slideNext();
});

function tabInit() {
    const tabs = document.querySelectorAll('.tab');
    
    for (let i = 0; i < tabs.length; i++) {
        const tabItems = tabs[i].querySelectorAll('.tab-item');
        const tabContents = tabs[i].querySelectorAll('.tab-content');
        
        tabItems.forEach((tabItem, index) => {
            tabItem.addEventListener('click', e => {
                for (let j = 0; j < tabContents.length; j++) {
                    tabItems[j].classList.remove('active');
                    tabContents[j].classList.remove('active');
                }
                
                tabItem.classList.add('active');
                tabContents[index].classList.add('active');
            });
        });
    }
}

/* 모바일에서 텍스트 애니메이션 */
function mobileScrollAnimation() {
    const animationItems = document.querySelectorAll('.animate__animated');
    const itemActive = (animationItems) => {
        const threshold = window.scrollY + window.innerHeight;
        
        for (let i = 0; i < animationItems.length; i++) {
            const item = animationItems[i];
            
            if (!item.closest('.swiper')) {
                const itemTop = this.scrollY + item.getBoundingClientRect().top;
                
                if (threshold > itemTop) {
                    item.classList.add('animate__fadeInUp');
                }
            }
            
            if (item.classList.contains('swiper')) {
                const itemTop = this.scrollY + item.getBoundingClientRect().top;
                
                if (threshold > itemTop) {
                    item.classList.add('animate__fadeInUp');
                }
            }
        }
    }
    
    itemActive(animationItems);
    
    if (window.innerWidth < 1025) {
        window.addEventListener('scroll', e => {
            itemActive(animationItems);
        });
    }
}

window.onload = function () {
    tabInit();
    swiperPaginationNumber(introSwiper);
    swiperVideoPlaying(introSwiper);
    swiperAutoplayControl(introSwiper);
    swiperEachProgressbar(techSwiper);
    swiperAnimatedActive(techSwiper);
    swiperPaginationNumber(techSwiper, '.tech');
    swiperPaginationNumber(rndSwiperTop, '.rnd');
    mobileScrollAnimation();
}