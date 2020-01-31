//jQuery to collapse the navbar on scroll
$(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
		$("#plugin-breadcrumb").addClass("breadcrumb-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
		$("#plugin-breadcrumb").removeClass("breadcrumb-collapse");
    }
});

//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('.page-scroll a').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});

// The function actually applying the offset
function offsetAnchor() {
    // This if statement is optional. It is just making sure that
    // there is a valid anchor to offset from.
    if($(location.hash).length !== 0) {
        window.scrollTo(window.scrollX, window.scrollY - 100);
    }
}

// This will capture hash changes while you are on the same page
$(window).on("hashchange", function () {
    offsetAnchor();
});

// This is here so that when you enter the page with a hash,
// it can provide the offset in that case too. Having a timeout
// seems necessary to allow the browser to jump to the anchor first.
window.setTimeout(function() {
    offsetAnchor();
}, 1); // The delay of 1 is arbitrary and may not always work right (although it did in my testing).
