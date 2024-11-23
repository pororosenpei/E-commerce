// Modal functionality
function showModal(message) {
    const modal = document.getElementById('success-modal');
    const modalMessage = document.getElementById('modal-message');
    modalMessage.textContent = message;
    modal.classList.add('active');
    setTimeout(() => {
        closeModal();
    }, 3000); // Automatically close after 3 seconds
}

function closeModal() {
    const modal = document.getElementById('success-modal');
    modal.classList.remove('active');
}

// Add to cart form submission
document.getElementById('add-to-cart-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('cart.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal('Product added to cart successfully!');
                closeCartModal();
                updateCartCount();
            } else {
                showModal('Error adding product to cart. Please try again.');
            }
        }).catch(error => {
            console.error('Error:', error);
            showModal('An error occurred. Please try again.');
        });
});
function closeModal(index) {
    document.getElementById('modal-' + index).classList.remove('active');
}

// Wishlist functionality
function toggleWishlist(productId, element) {
    const action = element.classList.contains('filled') ? 'remove' : 'add';
    $.post('home.php', { action: action, product_id: productId }, function(response) {
        element.classList.toggle('filled');
        if (action === 'remove' && $('#wishlist-section').is(':visible')) {
            $(element).closest('.product-card').fadeOut();
        }
    });
}

// Sidebar functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    sidebar.classList.toggle('open');
    content.classList.toggle('sidebar-open');
}

// Show/hide sections
function showSection(sectionId) {
    if (sectionId === 'home') {
        $('#home-section').show();
        $('#wishlist-section').hide();
    } else if (sectionId === 'wishlist') {
        $('#home-section').hide();
        $('#wishlist-section').show();
    }
    toggleSidebar();
}

// Cart modal functionality
function openCartModal(product) {
    const modal = document.getElementById('cart-modal');
    const productDetails = document.getElementById('cart-product-details');
    const productIdInput = document.getElementById('cart-product-id');

    productDetails.innerHTML = `
        <div class="flex items-center mb-4">
            <img src="${product.image || 'https://via.placeholder.com/100x100.png?text=No+Image'}" alt="${product.name}" class="w-20 h-20 object-cover rounded-md mr-4">
            <div>
                <h3 class="font-semibold">${product.name}</h3>
                <p class="text-gray-600">₱${parseFloat(product.price).toFixed(2)}</p>
            </div>
        </div>
    `;
    productIdInput.value = product.id;

    modal.classList.add('active');
}

function closeCartModal() {
    const modal = document.getElementById('cart-modal');
    modal.classList.remove('active');
}

// Add to cart form submission
document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
            closeCartModal();
            updateCartCount();
        } else {
            alert('Error adding product to cart: ' + (data.message || 'Please try again.'));
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

function updateCartCount() {
    fetch('cart.php?action=get_count')
    .then(response => response.json())
    .then(data => {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = data.count;
        }
    });
}

// Call updateCartCount when the page loads
document.addEventListener('DOMContentLoaded', updateCartCount);

// Add to cart form submission
document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
            closeCartModal();
        } else {
            alert('Error adding product to cart. Please try again.');
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
function closeModal(index) {
    document.getElementById('modal-' + index).classList.remove('active');
}

// Wishlist functionality
function toggleWishlist(productId, element) {
    const action = element.classList.contains('filled') ? 'remove' : 'add';
    $.post('home.php', { action: action, product_id: productId }, function(response) {
        element.classList.toggle('filled');
        if (action === 'remove' && $('#wishlist-section').is(':visible')) {
            $(element).closest('.product-card').fadeOut();
        }
    });
}
