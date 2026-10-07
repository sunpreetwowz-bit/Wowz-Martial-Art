import './bootstrap';

import Alpine from 'alpinejs';
import { initPublicMotion } from './public-motion';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initPublicMotion();
});
