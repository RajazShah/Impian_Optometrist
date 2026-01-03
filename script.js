// Wait for the HTML document to be fully loaded before running any script
document.addEventListener("DOMContentLoaded", function() {

    /* --- Lenses Slider Logic --- */
    const gridLenses = document.getElementById("lenses-grid");
    const leftArrowLenses = document.getElementById("lenses-arrow-left");
    const rightArrowLenses = document.getElementById("lenses-arrow-right");

    if (gridLenses && leftArrowLenses && rightArrowLenses) {
        let currentIndex = 0;
        const cardWidth = 220; // Matches your CSS
        const cardGap = 30;    // Matches your CSS
        const slideDistance = cardWidth + cardGap;
        const totalCards = gridLenses.querySelectorAll(".product-card").length;
        const visibleCards = 3;

        rightArrowLenses.onclick = function(event) {
            event.preventDefault();
            if (currentIndex < totalCards - visibleCards) {
                currentIndex++;
                gridLenses.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };

        leftArrowLenses.onclick = function(event) {
            event.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                gridLenses.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
    }

    /* --- Best Selling Slider Logic --- */
    const gridBest = document.getElementById("best-grid");
    const leftArrowBest = document.getElementById("best-arrow-left");
    const rightArrowBest = document.getElementById("best-arrow-right");

    if (gridBest && leftArrowBest && rightArrowBest) {
        let currentIndex = 0;
        const cardWidth = 220;
        const cardGap = 30;
        const slideDistance = cardWidth + cardGap;
        const totalCards = gridBest.querySelectorAll(".product-card").length;
        const visibleCards = 3;

        rightArrowBest.onclick = function(event) {
            event.preventDefault();
            if (currentIndex < totalCards - visibleCards) {
                currentIndex++;
                gridBest.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
        leftArrowBest.onclick = function(event) {
            event.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                gridBest.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
    }

    /* --- Contact Lense Slider Logic --- */
    const gridContact = document.getElementById("contact-grid");
    const leftArrowContact = document.getElementById("contact-arrow-left");
    const rightArrowContact = document.getElementById("contact-arrow-right");

    if (gridContact && leftArrowContact && rightArrowContact) {
        let currentIndex = 0;
        const cardWidth = 220;
        const cardGap = 30;
        const slideDistance = cardWidth + cardGap;
        const totalCards = gridContact.querySelectorAll(".product-card").length;
        const visibleCards = 3;

        rightArrowContact.onclick = function(event) {
            event.preventDefault();
            if (currentIndex < totalCards - visibleCards) {
                currentIndex++;
                gridContact.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
        leftArrowContact.onclick = function(event) {
            event.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                gridContact.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
    }

    /* --- Clip On Slider Logic --- */
    const gridClip = document.getElementById("clip-grid");
    const leftArrowClip = document.getElementById("clip-arrow-left");
    const rightArrowClip = document.getElementById("clip-arrow-right");

    if (gridClip && leftArrowClip && rightArrowClip) {
        let currentIndex = 0;
        const cardWidth = 220;
        const cardGap = 30;
        const slideDistance = cardWidth + cardGap;
        const totalCards = gridClip.querySelectorAll(".product-card").length;
        const visibleCards = 3;

        rightArrowClip.onclick = function(event) {
            event.preventDefault();
            if (currentIndex < totalCards - visibleCards) {
                currentIndex++;
                gridClip.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
        leftArrowClip.onclick = function(event) {
            event.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                gridClip.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
    }

    /* --- Modal & Dropdown Logic --- */
    var modal = document.getElementById("login-modal");
    var loginLink = document.getElementById("login-link");
    var loginToggle = document.getElementById("login-toggle");
    var registerToggle = document.getElementById("register-toggle");
    var loginForm = document.getElementById("login-form");
    var registerForm = document.getElementById("register-form");
    var userIcon = document.getElementById("user-icon-link");

    // Handles "Login to Add" buttons
    var loginTriggers = document.getElementsByClassName("login-trigger");
    for (var i = 0; i < loginTriggers.length; i++) {
        loginTriggers[i].onclick = function(event) {
            event.preventDefault();
            if(modal) {
                modal.style.display = "flex";
                if(loginForm) loginForm.style.display = "block";
                if(registerForm) registerForm.style.display = "none";
                if(loginToggle) loginToggle.classList.add("active");
                if(registerToggle) registerToggle.classList.remove("active");
            }
        }
    }

    if (loginLink) {
        loginLink.onclick = function(event) {
            event.preventDefault();
            modal.style.display = "flex";
            loginForm.style.display = "block";
            registerForm.style.display = "none";
            loginToggle.classList.add("active");
            registerToggle.classList.remove("active");
        }
    }

    if (registerToggle) {
        registerToggle.onclick = function(event) {
            event.preventDefault();
            loginForm.style.display = "none";
            registerForm.style.display = "block";
            loginToggle.classList.remove("active");
            registerToggle.classList.add("active");
        }
    }

    if (loginToggle) {
        loginToggle.onclick = function(event) {
            event.preventDefault();
            loginForm.style.display = "block";
            registerForm.style.display = "none";
            loginToggle.classList.add("active");
            registerToggle.classList.remove("active");
        }
    }
    
    if (userIcon) {
        userIcon.onclick = function(event) {
            event.preventDefault();
            this.nextElementSibling.classList.toggle("show");
        }
    }

    window.onclick = function(event) {
        if (modal && event.target == modal) {
            modal.style.display = "none";
        }
        
        if (userIcon && !event.target.closest('#user-icon-link')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    /* --- Cart.php Shipping & Total Calculator --- */
    const subtotalSpan = document.getElementById("subtotal-price");
    const totalSpan = document.getElementById("total-price");
    const radioDelivery = document.getElementById("radio-delivery");
    const radioPickup = document.getElementById("radio-pickup");
    const shippingRow = document.getElementById("shipping-row");
    const shippingFeeSpan = document.getElementById("shipping-fee");

    if (subtotalSpan && radioDelivery && radioPickup) {
        function updateCartTotal() {
            const subtotal = parseFloat(subtotalSpan.dataset.value);
            const deliveryFee = 10.00; 
            let newTotal;
            let finalShippingFee;

            if (radioDelivery.checked) {
                finalShippingFee = deliveryFee;
                shippingFeeSpan.innerText = "RM " + finalShippingFee.toFixed(2);
                shippingRow.style.display = "flex"; 
                newTotal = subtotal + finalShippingFee;
            } else {
                finalShippingFee = 0.00;
                shippingFeeSpan.innerText = "RM 0.00"; 
                shippingRow.style.display = "flex"; 
                newTotal = subtotal; 
            }
            totalSpan.innerText = "RM " + newTotal.toFixed(2);
        }
        
        radioDelivery.addEventListener("change", updateCartTotal);
        radioPickup.addEventListener("change", updateCartTotal);
        updateCartTotal(); 
    }
    
    /* --- Add To Cart Logic --- */
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const itemId = event.target.dataset.itemId;
            if (!itemId) return; 
            
            const formData = new FormData();
            formData.append('item_id', itemId);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.success) {
                    const cartBadge = document.getElementById('cart-badge-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.new_cart_count;
                        cartBadge.style.display = 'flex'; 
                    }
                    showToastPopup();
                } else {
                    console.error('Add to cart failed.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        });
    });

    function showToastPopup() {
        const toast = document.getElementById('toast-popup');
        if(toast) {
            toast.classList.add('show');
            setTimeout(function() {
                toast.classList.remove('show');
            }, 2000); 
        }
    }
    
    /* --- Main Slider Logic --- */
    const mainTrack = document.querySelector('.main-slider-track');
    
    if (mainTrack) {
        const slides = Array.from(mainTrack.children);
        const nextButton = document.getElementById('main-arrow-right');
        const prevButton = document.getElementById('main-arrow-left');
        const navLinks = document.querySelectorAll('.main-nav-link');
        
        const moveToSlide = (currentSlide, targetSlide) => {
            if (!targetSlide) return; 
            
            const slideWidth = targetSlide.getBoundingClientRect().width;
            const slideIndex = slides.findIndex(slide => slide === targetSlide);
            
            mainTrack.style.transform = 'translateX(-' + (slideWidth * slideIndex) + 'px)';
            currentSlide.classList.remove('is-current-slide');
            targetSlide.classList.add('is-current-slide');
        };

        const updateArrows = (targetIndex) => {
            if (targetIndex === 0) {
                prevButton.classList.add('is-hidden');
                nextButton.classList.remove('is-hidden');
            } else if (targetIndex === slides.length - 1) {
                prevButton.classList.remove('is-hidden');
                nextButton.classList.add('is-hidden');
            } else {
                prevButton.classList.remove('is-hidden');
                nextButton.classList.remove('is-hidden');
            }
        };

        if (slides.length > 0) {
            const slideMap = {
                '#frames-section': 1,
                '#contact-section': 2,
                '#clip-section': 3
            };

            const currentHash = window.location.hash;
            let targetIndex = 0; 
            if (currentHash && slideMap.hasOwnProperty(currentHash)) {
                targetIndex = slideMap[currentHash];
            }
            
            const targetSlide = slides[targetIndex];
            const slideWidth = targetSlide.getBoundingClientRect().width;
            
            mainTrack.style.transition = 'none'; 
            mainTrack.style.transform = 'translateX(-' + (slideWidth * targetIndex) + 'px)';
            targetSlide.classList.add('is-current-slide');
            updateArrows(targetIndex);
            
            setTimeout(() => {
                mainTrack.style.transition = 'transform 0.5s ease-in-out'; 
            }, 50);
        }

        nextButton.addEventListener('click', e => {
            const currentSlide = mainTrack.querySelector('.is-current-slide');
            const nextSlide = currentSlide.nextElementSibling;
            moveToSlide(currentSlide, nextSlide);
            const nextIndex = slides.findIndex(slide => slide === nextSlide);
            updateArrows(nextIndex);
        });

        prevButton.addEventListener('click', e => {
            const currentSlide = mainTrack.querySelector('.is-current-slide');
            const prevSlide = currentSlide.previousElementSibling;
            moveToSlide(currentSlide, prevSlide);
            const prevIndex = slides.findIndex(slide => slide === prevSlide);
            updateArrows(prevIndex);
        });

        navLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault(); 
                
                const targetIndex = parseInt(link.dataset.slide, 10);
                const currentSlide = mainTrack.querySelector('.is-current-slide');
                const targetSlide = slides[targetIndex];
                
                moveToSlide(currentSlide, targetSlide);
                updateArrows(targetIndex);
                
                // Scroll to hero section smoothly
                const heroSection = document.querySelector('.hero-section');
                if (heroSection) {
                    heroSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        window.addEventListener('resize', () => {
            const currentSlide = mainTrack.querySelector('.is-current-slide');
            if (currentSlide) { 
                const slideIndex = slides.findIndex(slide => slide === currentSlide);
                const slideWidth = currentSlide.getBoundingClientRect().width;
                mainTrack.style.transition = 'none'; 
                mainTrack.style.transform = 'translateX(-' + (slideWidth * slideIndex) + 'px)';
                setTimeout(() => {
                    mainTrack.style.transition = 'transform 0.5s ease-in-out'; 
                }, 50);
            }
        });
    }

    /* --- Profile Picture Preview --- */
    const profilePictureInput = document.getElementById("profilePictureInput");
    const profileImagePreview = document.getElementById("profileImagePreview"); 
    
    if (profilePictureInput && profileImagePreview) {
        profilePictureInput.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /* --- URL Parameter Alerts (Upload/Error) --- */
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('upload');
    
    if (error) {
        alert('Upload Error: ' + error);
        window.history.replaceState(null, '', window.location.pathname);
    }
    if (success === 'success') {
        alert('Profile picture updated successfully!');
        window.history.replaceState(null, '', window.location.pathname);
    }

    /* --- NEW: FORM VALIDATION LOGIC --- */
    
    // 1. Appointment Form Validation
    const appointmentForm = document.querySelector('form[action="appointment-process.php"]');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(event) {
            // Check all required inputs in this specific form
            const requiredInputs = appointmentForm.querySelectorAll('input[required], select[required]');
            let isEmpty = false;

            requiredInputs.forEach(function(input) {
                if (!input.value.trim()) {
                    isEmpty = true;
                }
            });

            if (isEmpty) {
                event.preventDefault(); // Stop the form from submitting
                alert("Please fill in all required fields before booking.");
            }
        });
    }

    // 2. Registration Form Validation (Generic for any form with class 'register-form' or action)
    const registerFormEl = document.querySelector('form[action="register-process.php"]');
    if (registerFormEl) {
        registerFormEl.addEventListener('submit', function(event) {
            const requiredInputs = registerFormEl.querySelectorAll('input[required], select[required]');
            let isEmpty = false;

            requiredInputs.forEach(function(input) {
                if (!input.value.trim()) {
                    isEmpty = true;
                }
            });

            if (isEmpty) {
                event.preventDefault(); 
                alert("Please fill in all required fields to register.");
            }
        });
    }

});