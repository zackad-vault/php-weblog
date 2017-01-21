var popover = document.querySelectorAll('[data-popover]');
var $nav = document.querySelector('.navbar');
var $body = document.querySelector('body');
var $window = document.querySelector('window');
var navOffsetTop = $nav.getBoundingClientRect();

for (var i = 0; i < popover.length; i++) {
    popover[i].addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = this;
        closePopover(self);
        openPopover(self);
    });
}
document.addEventListener('click', closePopover);

function closePopover(el) {
    var popover = document.querySelectorAll('.popover.open');
    if (popover.length > 0) {
        for (var i = 0; i < popover.length; i++) {
            popover[i].classList.remove('open');
        }
    }
}
function onScroll() {
    if (navOffsetTop < $window.scrollTop() && !$body.hasClass('has-docked-nav')) {
        $body.addClass('has-docked-nav')
    }
    if (navOffsetTop > $window.scrollTop() && $body.hasClass('has-docked-nav')) {
        $body.removeClass('has-docked-nav')
    }
}

function openPopover(el) {
    if (el.nextElementSibling.classList.contains('open')) {
        el.nextElementSibling.classList.remove('open');
    } else {
        el.nextElementSibling.classList.add('open');
    }
}

function toggleMenu() {
    var navbarItems = document.querySelectorAll('.navbar-mobile .navbar-item');
    for (var i = 0; i < navbarItems.length; i++) {
        if (navbarItems[i].classList.contains('active')) {
            navbarItems[i].classList.remove('active');
        } else {
            navbarItems[i].classList.add('active');
        }
    }
}
