// Wait for the HTML document to be fully loaded before running any script
document.addEventListener("DOMContentLoaded", function() {

    /* --- Frames Slider Logic --- */
    const gridFrames = document.getElementById("frame-grid");
    const leftArrowFrames = document.getElementById("frame-arrow-left");
    const rightArrowFrames = document.getElementById("frame-arrow-right");

    if (gridFrames && leftArrowFrames && rightArrowFrames) {
        let currentIndex = 0;
        const cardWidth = 250;
        const cardGap = 30;
        const slideDistance = cardWidth + cardGap;
        const totalCards = gridFrames.querySelectorAll(".product-card").length;
        const visibleCards = 3;

        rightArrowFrames.onclick = function(event) {
            event.preventDefault();
            if (currentIndex < totalCards - visibleCards) {
                currentIndex++;
                gridFrames.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
        leftArrowFrames.onclick = function(event) {
            event.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
                gridFrames.style.transform = `translateX(-${currentIndex * slideDistance}px)`;
            }
        };
    }

    /* --- Best Selling Slider Logic (NEW) --- */
    const gridBest = document.getElementById("best-grid");
    const leftArrowBest = document.getElementById("best-arrow-left");
    const rightArrowBest = document.getElementById("best-arrow-right");

    if (gridBest && leftArrowBest && rightArrowBest) {
        let currentIndex = 0;
        const cardWidth = 250;
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
        const cardWidth = 250;
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
        const cardWidth = 250;
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

    // This handles the "Login to Add" buttons on product cards
    var loginTriggers = document.getElementsByClassName("login-trigger");
    for (var i = 0; i < loginTriggers.length; i++) {
        loginTriggers[i].onclick = function(event) {
            event.preventDefault();
            modal.style.display = "flex";
            loginForm.style.display = "block";
            registerForm.style.display = "none";
            loginToggle.classList.add("active");
            registerToggle.classList.remove("active");
        }
    }
    var userIcon = document.getElementById("user-icon-link");
    var loginTriggers = document.getElementsByClassName("login-trigger");
    for (var i = 0; i < loginTriggers.length; i++) {
        loginTriggers[i].onclick = function(event) {
            event.preventDefault(); 
            modal.style.display = "flex";
            loginForm.style.display = "block";
            registerForm.style.display = "none";
            loginToggle.classList.add("active");
            registerToggle.classList.remove("active");
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

    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    
    // 2. Loop through each button and add a click listener
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            
            // Get the item ID from the button's 'data-item-id' attribute
            const itemId = event.target.dataset.itemId;
            
            if (!itemId) return; // Not a valid add to cart button
            
            // Create form data to send to PHP
            const formData = new FormData();
            formData.append('item_id', itemId);

            // 3. Send the data to add_to_cart.php in the background
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Get the JSON response from PHP
            .then(data => {
                
                if (data.success) {
                    // 4. Update the cart badge
                    const cartBadge = document.getElementById('cart-badge-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.new_cart_count;
                        cartBadge.style.display = 'flex'; // Show it
                    }

                    // 5. Show the "Added to Cart" popup
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

    // Function to show and hide the popup
    function showToastPopup() {
        const toast = document.getElementById('toast-popup');
        
        // Add the 'show' class to make it visible
        toast.classList.add('show');

        // After 2 seconds, remove the 'show' class to fade it out
        setTimeout(function() {
            toast.classList.remove('show');
        }, 2000); // 2000 milliseconds = 2 seconds
    }
    
}); 