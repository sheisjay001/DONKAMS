// Toast Notification System
function showToast(title, message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    toast.innerHTML = `
        <i class="fas ${icon}"></i>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;

    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    // Mobile Navigation & Focus Management
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const body = document.body;

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
            
            // Toggle State
            hamburger.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('active');
            
            // Lock Scroll when menu is open
            if (!isExpanded) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navLinks.classList.contains('active') && 
                !navLinks.contains(e.target) && 
                !hamburger.contains(e.target)) {
                
                navLinks.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });

        // Close menu on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });
    }

    // Smooth Scroll for Anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            // Only scroll if it's a hash link on the same page
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Close mobile menu if open
                    if (navLinks && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        hamburger.setAttribute('aria-expanded', 'false');
                        body.style.overflow = '';
                    }

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update focus for accessibility
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                }
            }
        });
    });

    // --- Enhanced Form Validation ---
    const forms = document.querySelectorAll('form[novalidate]');

    const getErrorMessage = (input) => {
        if (input.validity.valueMissing) return 'This field is required.';
        if (input.validity.typeMismatch) {
            if (input.type === 'email') return 'Please enter a valid email address.';
            if (input.type === 'url') return 'Please enter a valid URL.';
        }
        if (input.validity.tooShort) return `Please lengthen this text to ${input.minLength} characters or more.`;
        if (input.validity.patternMismatch) return 'Please match the requested format.';
        if (input.validity.rangeUnderflow) return `Value must be ${input.min} or higher.`;
        if (input.validity.rangeOverflow) return `Value must be ${input.max} or lower.`;
        return 'The value you entered for this field is invalid.';
    };

    const showError = (input, message) => {
        input.classList.add('error');
        input.classList.remove('valid');
        input.setAttribute('aria-invalid', 'true');

        let errorContainer = input.parentNode.querySelector('.error-message');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'error-message';
            // Generate a unique ID for the error message
            const errorId = 'error-' + Math.random().toString(36).substring(2, 9);
            errorContainer.id = errorId;
            input.setAttribute('aria-describedby', errorId);
            input.parentNode.insertBefore(errorContainer, input.nextSibling);
        }
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
    };

    const hideError = (input) => {
        input.classList.remove('error');
        input.setAttribute('aria-invalid', 'false');
        input.removeAttribute('aria-describedby');

        const errorContainer = input.parentNode.querySelector('.error-message');
        if (errorContainer) {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }
    };

    const validateInput = (input) => {
        if (input.checkValidity()) {
            hideError(input);
            // Optionally add a 'valid' class for styling
            if (input.value.length > 0) {
                input.classList.add('valid');
            }
            return true;
        } else {
            const message = getErrorMessage(input);
            showError(input, message);
            return false;
        }
    };

    forms.forEach(form => {
        // Validate on blur
        form.addEventListener('blur', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                validateInput(e.target);
            }
        }, true); // Use capture phase to catch blur events on all fields

        // Validate on input for instant feedback
        form.addEventListener('input', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                // Only show errors after the first blur (or submit attempt)
                if (e.target.classList.contains('error')) {
                    validateInput(e.target);
                }
            }
        }, true);

        // Validate on submit
        form.addEventListener('submit', function(e) {
            let isFormValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                showToast('Error', 'Please correct the errors before submitting.', 'error');
                // Focus the first invalid field
                const firstInvalid = form.querySelector('[aria-invalid="true"]');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    });

    // Add to Cart Functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const originalText = this.innerHTML; // Use innerHTML to preserve icons
            
            // Loading State
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            this.disabled = true;

            fetch('cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update cart count
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.innerText = data.cart_count;
                        
                        // Pulse animation for cart icon
                        cartCountElement.parentElement.classList.add('pulse');
                        setTimeout(() => cartCountElement.parentElement.classList.remove('pulse'), 500);
                    }
                    
                    this.innerHTML = '<i class="fas fa-check"></i> Added!';
                    this.style.backgroundColor = 'var(--success-color)';
                    showToast('Success', 'Product added to your cart!', 'success');

                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        this.style.backgroundColor = '';
                    }, 2000);
                } else {
                    showToast('Error', 'Failed to add product to cart', 'error');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Something went wrong', 'error');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
    
    // Wishlist Functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const icon = this.querySelector('i');
            const adding = icon.classList.contains('far');
            const action = adding ? 'add' : 'remove';
            
            // Optimistic UI Update
            if (adding) {
                icon.classList.replace('far', 'fas');
                icon.classList.add('pulse');
            } else {
                icon.classList.replace('fas', 'far');
            }

            fetch('wishlist_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&product_id=${productId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    const msg = adding ? 'Added to wishlist' : 'Removed from wishlist';
                    showToast('Wishlist', msg, 'success');
                } else {
                    // Revert on error
                    if (adding) {
                        icon.classList.replace('fas', 'far');
                    } else {
                        icon.classList.replace('far', 'fas');
                    }
                    showToast('Wishlist', data.message || 'Action failed', 'error');
                }
                icon.classList.remove('pulse');
            })
            .catch(() => {
                // Revert on network error
                if (adding) {
                    icon.classList.replace('fas', 'far');
                } else {
                    icon.classList.replace('far', 'fas');
                }
                showToast('Wishlist', 'Network error', 'error');
                icon.classList.remove('pulse');
            });
        });
    });
});
