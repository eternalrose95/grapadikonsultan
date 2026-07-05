import './bootstrap';
import intersect from '@alpinejs/intersect';

// Register Alpine.js intersect plugin
document.addEventListener('alpine:init', () => {
    Alpine.plugin(intersect);
});

// Scroll Animation Observer
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    if (animatedElements.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const animation = el.dataset.animate || 'fade-in-up';
                const delay = el.dataset.delay || '0';
                
                el.style.animationDelay = `${delay}ms`;
                el.classList.add(`animate-${animation}`);
                el.classList.add('animated');
                
                observer.unobserve(el);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
});
