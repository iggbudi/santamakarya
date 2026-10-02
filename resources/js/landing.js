import { createIcons, icons } from 'lucide';
import { initSlideshow } from './slideshow';

createIcons({ icons });

const mobileMenuButton = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
mobileMenuButton?.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    mobileMenuButton.setAttribute('aria-expanded', String(!mobileMenu.classList.contains('hidden')));
});
document.querySelectorAll('#mobile-menu a').forEach(link => {
    link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
        mobileMenuButton.setAttribute('aria-expanded', 'false');
    });
});
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        mobileMenuButton?.setAttribute('aria-expanded', 'false');
        mobileMenuButton?.focus();
    }
});

const cleanupSlides = [...document.querySelectorAll('[data-slideshow]')].map(initSlideshow);
window.addEventListener('pagehide', () => cleanupSlides.forEach(cleanup => cleanup()), { once: true });

document.querySelectorAll('.port-filter-btn').forEach(button => {
    button.addEventListener('click', () => {
        const category = button.dataset.filter;
        document.querySelectorAll('.port-filter-btn').forEach(item => {
            const active = item.dataset.filter === category;
            item.classList.toggle('bg-brand-charcoal', active);
            item.classList.toggle('text-white', active);
            item.classList.toggle('bg-gray-100', !active);
            item.classList.toggle('text-gray-700', !active);
        });
        document.querySelectorAll('.port-item').forEach(item => {
            item.style.display = category === 'all' || item.dataset.category === category ? 'block' : 'none';
        });
    });
});

