// Main JavaScript file for Reshina

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const authButtons = document.querySelector('.auth-buttons');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            authButtons.classList.toggle('active');
        });
    }
    
    // Search functionality
    const searchButton = document.querySelector('.search-button');
    const searchBar = document.querySelector('.search-bar');
    
    if (searchButton && searchBar) {
        searchButton.addEventListener('click', function() {
            const searchTerm = searchBar.value.trim();
            if (searchTerm) {
                // Redirect to search results page with the search term
                window.location.href = `pages/search-results.php?query=${encodeURIComponent(searchTerm)}`;
            }
        });
        
        // Allow search on Enter key
        searchBar.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = searchBar.value.trim();
                if (searchTerm) {
                    window.location.href = `pages/search-results.php?query=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }
    
    // Category cards click handler
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('.category-name').textContent;
            window.location.href = `pages/buy-claim.php?category=${encodeURIComponent(categoryName)}`;
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Initialize location services if available
    if (navigator.geolocation) {
        const locationIcon = document.querySelector('.location-icon');
        if (locationIcon) {
            locationIcon.style.color = '#00FF84'; // Change color to indicate location is available
            
            locationIcon.addEventListener('click', function() {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Store location in session storage for use across the site
                    sessionStorage.setItem('userLat', position.coords.latitude);
                    sessionStorage.setItem('userLng', position.coords.longitude);
                    
                    // Visual feedback
                    locationIcon.classList.add('pulse');
                    setTimeout(() => {
                        locationIcon.classList.remove('pulse');
                    }, 1000);
                    
                    // You could update the placeholder text to show that location is active
                    const searchBar = document.querySelector('.search-bar');
                    if (searchBar) {
                        searchBar.placeholder = 'Lokasi aktif - cari barang di sekitarmu...';
                    }
                });
            });
        }
    }
});
