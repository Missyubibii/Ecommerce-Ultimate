document.addEventListener('DOMContentLoaded', () => {

    // 1. Top Banner (Text Slider)
    if (document.querySelector('#top-banner-swiper')) {
        new Swiper('#top-banner-swiper', {
            direction: 'vertical',
            loop: true,
            autoplay: { delay: 3000, disableOnInteraction: false },
            allowTouchMove: false,
            height: 40
        });
    }

    // 2. Main Hero Banner
    if (document.querySelector('#main-banner-swiper')) {
        new Swiper('#main-banner-swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            effect: 'fade',
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }

    // 3. Product Category Sliders
    const productSwipers = document.querySelectorAll('.product-swiper');
    productSwipers.forEach(el => {
        const id = el.id.replace('swiper-cat-', ''); // Lấy ID danh mục

        new Swiper(el, {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: `.next-btn-${id}`,
                prevEl: `.prev-btn-${id}`,
            },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true, dynamicBullets: true },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 }, // Mobile large
                768: { slidesPerView: 3, spaceBetween: 24 }, // Tablet
                1024: { slidesPerView: 4, spaceBetween: 24 }, // Desktop
                1280: { slidesPerView: 5, spaceBetween: 24 }, // Large Desktop
            }
        });
    });
});
