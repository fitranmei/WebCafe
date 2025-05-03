function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

 // JavaScript for interactivity (search functionality would go here)
document.querySelector('.search-input').addEventListener('focus', function() {
    this.placeholder = '';
});

document.querySelector('.search-input').addEventListener('blur', function() {
    if (this.value === '') {
        this.placeholder = 'Cari menu...';
    }
});

// Menu tabs functionality
const tabs = document.querySelectorAll('.menu-tab');
tabs.forEach(tab => {
    tab.addEventListener('click', function() {
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

// Carousel Functionality
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    const items = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    let currentIndex = 0;
    const totalItems = items.length;
    
    // Function to update carousel position
    function updateCarousel() {
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Update active indicator
        indicators.forEach((indicator, index) => {
            if (index === currentIndex) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
    }
    
    // Previous button click
    prevBtn.addEventListener('click', function() {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalItems - 1;
        updateCarousel();
    });
    
    // Next button click
    nextBtn.addEventListener('click', function() {
        currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
        updateCarousel();
    });
    
    // Indicator clicks
    indicators.forEach(indicator => {
        indicator.addEventListener('click', function() {
            currentIndex = parseInt(this.getAttribute('data-index'));
            updateCarousel();
        });
    });
    
    // Auto slide every 5 seconds
    let autoSlide = setInterval(function() {
        currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
        updateCarousel();
    }, 3000);
    
    // Pause auto slide when hovering over carousel
    carousel.addEventListener('mouseenter', function() {
        clearInterval(autoSlide);
    });
    
    // Resume auto slide when mouse leaves carousel
    carousel.addEventListener('mouseleave', function() {
        autoSlide = setInterval(function() {
            currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        }, 5000);
    });
    
    // Touch support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        // Detect direction of swipe and change slide accordingly
        if (touchEndX < touchStartX - 50) {
            // Swipe left - next slide
            currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        } else if (touchEndX > touchStartX + 50) {
            // Swipe right - previous slide
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalItems - 1;
            updateCarousel();
        }
    }
});