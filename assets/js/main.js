/**
 * Main JavaScript for lotteryLK
 */

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // WhatsApp Order Button Click Handler
    const whatsappButtons = document.querySelectorAll('.whatsapp-order-btn');
    
    whatsappButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const lotteryName = this.dataset.lottery || '';
            const drawDate = this.dataset.date || '';
            
            // Base phone number - replace with your actual WhatsApp number
            const phone = '+94771234567';
            
            // Create message
            let message = "හෙලෝ, මට " + lotteryName + " ලොතරැයිය ඇණවුම් කිරීමට අවශ්‍යයි";
            
            if (drawDate) {
                message += " - දිනුම් ඇදීම් දිනය: " + drawDate;
            }
            
            // URL encode the message
            const encodedMessage = encodeURIComponent(message);
            
            // Open WhatsApp
            window.open(`https://wa.me/${phone}?text=${encodedMessage}`, '_blank');
        });
    });
    
    // Initialize Three.js lottery animation if the container exists
    const lotteryAnimationContainer = document.getElementById('lottery-animation');
    if (lotteryAnimationContainer) {
        initThreeJsAnimation(lotteryAnimationContainer);
    }
    
    // Initialize charts for patterns page if on the patterns page
    const numberFrequencyChart = document.getElementById('number-frequency-chart');
    if (numberFrequencyChart) {
        initNumberFrequencyChart(numberFrequencyChart);
    }
    
    // Handle lottery filter on results page
    const lotteryFilter = document.getElementById('lottery-filter');
    if (lotteryFilter) {
        lotteryFilter.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    }
    
    // Image lazy loading
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const image = entry.target;
                    image.src = image.dataset.src;
                    image.classList.remove('lazy');
                    imageObserver.unobserve(image);
                }
            });
        });
        
        lazyImages.forEach(image => {
            imageObserver.observe(image);
        });
    } else {
        // Fallback for browsers without IntersectionObserver support
        lazyImages.forEach(image => {
            image.src = image.dataset.src;
        });
    }
    
    // Initialize date pickers if any exist
    const dateInputs = document.querySelectorAll('.date-picker');
    if (dateInputs.length > 0) {
        dateInputs.forEach(input => {
            // You can initialize a date picker library here if needed
            input.type = 'date'; // Fallback to native date input
        });
    }
});

/**
 * Generate WhatsApp order link
 * @param {string} lotteryName - The name of the lottery
 * @param {string} drawDate - Optional draw date
 * @return {string} WhatsApp URL
 */
function generateWhatsAppOrderLink(lotteryName, drawDate = '') {
    const phone = '+94771234567'; // Replace with your actual WhatsApp number
    
    let message = "හෙලෝ, මට " + lotteryName + " ලොතරැයිය ඇණවුම් කිරීමට අවශ්‍යයි";
    
    if (drawDate) {
        message += " - දිනුම් ඇදීම් දිනය: " + drawDate;
    }
    
    // URL encode the message
    const encodedMessage = encodeURIComponent(message);
    
    return `https://wa.me/${phone}?text=${encodedMessage}`;
}

/**
 * Format currency for display
 * @param {number} amount - The amount to format
 * @return {string} Formatted amount
 */
function formatCurrency(amount) {
    return 'රු. ' + new Intl.NumberFormat('si-LK').format(amount);
}

/**
 * Initialize number frequency chart for patterns page
 * @param {HTMLElement} container - The chart container element
 */
function initNumberFrequencyChart(container) {
    // This assumes you have data attributes on the container with the frequency data
    // Example: <canvas id="number-frequency-chart" data-numbers="1,2,3,4" data-frequencies="5,3,7,2"></canvas>
    
    const numbers = container.dataset.numbers ? container.dataset.numbers.split(',') : [];
    const frequencies = container.dataset.frequencies ? container.dataset.frequencies.split(',').map(Number) : [];
    
    if (numbers.length > 0 && frequencies.length > 0) {
        const ctx = container.getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: numbers,
                datasets: [{
                    label: 'වාර ගණන',
                    data: frequencies,
                    backgroundColor: '#0A3D62',
                    borderColor: '#1E8449',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
}
