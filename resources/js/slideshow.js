export function initSlideshow(container) {
    const slides = [...container.querySelectorAll('[data-slide]')];
    const dots = [...document.querySelectorAll(`[data-slide-for="${container.id}"]`)];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0;
    let timer;
    const interval = Math.min(10000, Math.max(3000, Number(container.dataset.interval) || 5000));
    function show(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('slide-active', i === index);
            slide.classList.toggle('slide-inactive', i !== index);
            slide.setAttribute('aria-hidden', String(i !== index));
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('w-8', i === index);
            dot.classList.toggle('bg-brand-orange', i === index);
            dot.classList.toggle('w-2', i !== index);
            dot.classList.toggle('bg-white/40', i !== index);
            dot.setAttribute('aria-pressed', String(i === index));
        });
        current = index;
    }
    function start() {
        clearInterval(timer);
        dots.forEach(dot => { dot.hidden = slides.length < 2 || reduced.matches; });
        if (reduced.matches) show(0);
        else if (slides.length > 1) timer = setInterval(() => show((current + 1) % slides.length), interval);
    }
    const handlers = dots.map((dot, index) => {
        const handler = () => { if (!reduced.matches) { show(index); start(); } };
        dot.addEventListener('click', handler);
        return handler;
    });
    show(0); start();
    reduced.addEventListener('change', start);
    return () => {
        clearInterval(timer);
        dots.forEach((dot, i) => dot.removeEventListener('click', handlers[i]));
        reduced.removeEventListener('change', start);
    };
}
