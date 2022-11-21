function initFull() {
    let fullpagePrevIndex;
    $('#fullpage').fullpage({
        onLeave: function (index, nextIndex, direction) {
        },
        afterLoad: function (anchorLink, index) {
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