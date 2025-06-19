import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// resources/js/app.js
import Splide from '@splidejs/splide';

document.addEventListener('DOMContentLoaded', function () {
    const splideElements = document.querySelectorAll('.splide');
    splideElements.forEach(function (element) {
        new Splide(element).mount();
    });
});
