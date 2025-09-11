<!-- Floating Cart Icon -->
<div class="floating-cart" id="floatingCart">
    <div class="cart-icon">
        <i class="bi bi-cart3"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems">
                    <div class="text-center text-muted" id="emptyCart">
                        <i class="bi bi-cart-x fs-1"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total: ৳<span id="cartTotal">0</span></strong>
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="orderBtn" onclick="showOrderForm()">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Time Modal -->
<div class="modal fade" id="serviceTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Service Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Service Name:</label>
                    <p id="serviceName" class="fw-bold"></p>
                </div>
                <div class="mb-3">
                    <label for="serviceDate" class="form-label">Select Date *</label>
                    <input type="date" class="form-control" id="serviceDate" required>
                </div>
                <div class="mb-3">
                    <label for="serviceTime" class="form-label">Select Time *</label>
                    <select class="form-control" id="serviceTime" required>
                        <option value="">Choose time slot</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="14:00">02:00 PM</option>
                        <option value="15:00">03:00 PM</option>
                        <option value="16:00">04:00 PM</option>
                        <option value="17:00">05:00 PM</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmServiceTime()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Order Form Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" id="customerPhone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Address *</label>
                        <textarea class="form-control" id="customerAddress" rows="3" required></textarea>
                    </div>
                    <div class="border p-3 bg-light">
                        <h6>Order Summary:</h6>
                        <div id="orderSummary"></div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total: ৳<span id="orderTotal">0</span></strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitOrderBtn">
                        Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fixed JavaScript -->
<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchIcon = document.getElementById('searchIcon');
    
    if (searchInput && searchIcon) {
        searchInput.addEventListener('input', () => {
            if(searchInput.value.length > 0){
                searchIcon.style.display = 'block';
                searchInput.classList.remove('text-center');
                searchInput.classList.add('text-start');
            } else {
                searchIcon.style.display = 'none';
                searchInput.classList.remove('text-start');
                searchInput.classList.add('text-center');
            }
        });
    }
});

function handleSearch(event) {
    event.preventDefault();
    const query = document.getElementById('searchInput').value.trim();
    if (query) {
        console.log('Searching for:', query);
    }
}

// Fixed Cart System
class SimpleCart {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('shopping_cart')) || [];
        this.currentServiceItem = null;
        this.processing = false;
        this.init();
    }

    init() {
        this.updateCartCount();
        this.bindEvents();
        this.setMinDate();
    }

    setMinDate() {
        setTimeout(() => {
            const serviceDate = document.getElementById('serviceDate');
            if (serviceDate) {
                const today = new Date().toISOString().split('T')[0];
                serviceDate.min = today;
            }
        }, 100);
    }

    bindEvents() {
    const floatingCart = document.getElementById('floatingCart');
    if (floatingCart) {
        floatingCart.addEventListener('click', () => {
            this.showCartModal();
        });
    }

    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', (e) => {
            this.submitOrder(e);
        });
    }

    // ADD THIS: Event delegation for cart operations
    document.addEventListener('click', (e) => {
        const action = e.target.getAttribute('data-action');
        const index = parseInt(e.target.getAttribute('data-index'));
        
        if (action && !isNaN(index)) {
            if (action === 'increase') {
                this.updateQuantity(index, this.cart[index].quantity + 1);
            } else if (action === 'decrease') {
                this.updateQuantity(index, this.cart[index].quantity - 1);
            } else if (action === 'remove') {
                this.removeFromCart(index);
            }
        }
    });

    // ADD THIS: Event delegation for quantity input
    document.addEventListener('change', (e) => {
        if (e.target.getAttribute('data-action') === 'change') {
            const index = parseInt(e.target.getAttribute('data-index'));
            if (!isNaN(index)) {
                this.updateQuantity(index, parseInt(e.target.value));
            }
        }
    });
}

    addToCart(productId, productName, productPrice, productImage, categoryType = 'product') {
        if (this.processing) return;
        this.processing = true;
        setTimeout(() => this.processing = false, 1000);

        if (categoryType === 'service') {
            this.currentServiceItem = {
                id: productId,
                name: productName,
                price: parseFloat(productPrice) || 0,
                image: productImage,
                type: 'service'
            };
            
            const serviceName = document.getElementById('serviceName');
            if (serviceName) {
                serviceName.textContent = productName;
            }
            
            const serviceModal = new bootstrap.Modal(document.getElementById('serviceTimeModal'));
            serviceModal.show();
            return;
        }

        this.addItemToCart(productId, productName, productPrice, productImage, 'product');
    }

    addItemToCart(productId, productName, productPrice, productImage, type, serviceTime = null) {
        const existingItem = this.cart.find(item => 
            item.id === productId && 
            (type === 'product' || item.service_time === serviceTime)
        );
        
        if (existingItem && type === 'product') {
            existingItem.quantity += 1;
        } else {
            const newItem = {
                id: productId,
                name: productName,
                price: parseFloat(productPrice) || 0,
                image: productImage,
                quantity: 1,
                type: type
            };

            if (serviceTime) {
                newItem.service_time = serviceTime;
            }

            this.cart.push(newItem);
        }
        
        this.saveCart();
        this.updateCartCount();
        this.showAnimation();
        this.showToast(`${productName} added to cart!`, 'success');
    }

    removeFromCart(itemIndex) {
        this.cart.splice(itemIndex, 1);
        this.saveCart();
        this.updateCartCount();
        this.updateCartModal();
    }

    updateQuantity(itemIndex, newQuantity) {
        if (newQuantity <= 0) {
            this.removeFromCart(itemIndex);
            return;
        }
        
        if (this.cart[itemIndex]) {
            this.cart[itemIndex].quantity = parseInt(newQuantity);
            this.saveCart();
            this.updateCartModal();
            this.updateCartCount();
        }
    }

    saveCart() {
        localStorage.setItem('shopping_cart', JSON.stringify(this.cart));
    }

    updateCartCount() {
        const cartCount = document.getElementById('cartCount');
        if (!cartCount) return;
        
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        
        cartCount.textContent = totalItems;
        
        if (totalItems > 0) {
            cartCount.classList.add('show');
        } else {
            cartCount.classList.remove('show');
        }
    }

    showCartModal() {
        this.updateCartModal();
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        cartModal.show();
    }

    updateCartModal() {
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const cartTotal = document.getElementById('cartTotal');
    
    if (!cartItems || !emptyCart || !cartTotal) return;
    
    if (this.cart.length === 0) {
        emptyCart.style.display = 'block';
        cartTotal.textContent = '0';
        return;
    }
    
    emptyCart.style.display = 'none';
    
    let html = '';
    let total = 0;
    
    this.cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        const serviceTimeDisplay = item.service_time ? 
            `<small class="text-muted d-block">Service Time: ${new Date(item.service_time).toLocaleString()}</small>` : '';
        
        const quantityControls = item.type === 'service' ? 
            `<span class="badge bg-info">Service Booking</span>` :
            `<div class="quantity-controls">
                <button class="quantity-btn" data-action="decrease" data-index="${index}">-</button>
                <input type="number" class="quantity-input" value="${item.quantity}" 
                       data-action="change" data-index="${index}" min="1">
                <button class="quantity-btn" data-action="increase" data-index="${index}">+</button>
            </div>`;
        
        html += `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">৳${item.price}</div>
                    ${serviceTimeDisplay}
                    ${quantityControls}
                </div>
                <div>
                    <div class="text-end mb-2">৳${itemTotal}</div>
                    <button class="remove-btn" data-action="remove" data-index="${index}">Remove</button>
                </div>
            </div>
        `;
    });
    
    cartItems.innerHTML = html;
    cartTotal.textContent = total.toFixed(2);
}

    showAnimation() {
        const cartIcon = document.querySelector('.cart-icon');
        if (cartIcon) {
            cartIcon.classList.add('cart-animate');
            setTimeout(() => {
                cartIcon.classList.remove('cart-animate');
            }, 600);
        }
    }

    showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        
        setTimeout(() => {
            const toasts = document.querySelectorAll('.toast');
            if (toasts.length > 0) {
                toasts[toasts.length - 1].remove();
            }
        }, 3000);
    }

    async submitOrder(e) {
        e.preventDefault();
        
        if (this.cart.length === 0) {
            this.showToast('Your cart is empty!', 'error');
            return;
        }
        
        const phone = document.getElementById('customerPhone').value;
        const address = document.getElementById('customerAddress').value;
        const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        const submitBtn = document.getElementById('submitOrderBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Placing Order...';
        
        try {
            const response = await fetch('/orders/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: phone,
                    shipping_address: address,
                    total_amount: total,
                    cart_items: this.cart
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.cart = [];
                this.saveCart();
                this.updateCartCount();
                
                bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
                
                this.showToast('Order placed successfully!', 'success');
                document.getElementById('orderForm').reset();
            } else {
                this.showToast(data.message || 'Order failed!', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Something went wrong!', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Confirm Order';
        }
    }
}

// Global functions
function confirmServiceTime() {
    const serviceDate = document.getElementById('serviceDate').value;
    const serviceTime = document.getElementById('serviceTime').value;
    
    if (!serviceDate || !serviceTime) {
        alert('Please select both date and time!');
        return;
    }
    
    const serviceDateTime = `${serviceDate} ${serviceTime}:00`;
    
    if (window.cart && window.cart.currentServiceItem) {
        window.cart.addItemToCart(
            window.cart.currentServiceItem.id,
            window.cart.currentServiceItem.name,
            window.cart.currentServiceItem.price,
            window.cart.currentServiceItem.image,
            'service',
            serviceDateTime
        );
        
        window.cart.currentServiceItem = null;
        bootstrap.Modal.getInstance(document.getElementById('serviceTimeModal')).hide();
        
        document.getElementById('serviceDate').value = '';
        document.getElementById('serviceTime').value = '';
    }
}

function showOrderForm() {
    if (!window.cart || window.cart.cart.length === 0) {
        return;
    }
    
    const orderSummary = document.getElementById('orderSummary');
    const orderTotal = document.getElementById('orderTotal');
    
    let html = '';
    let total = 0;
    
    window.cart.cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        const serviceTimeText = item.service_time ? 
            ` (${new Date(item.service_time).toLocaleString()})` : '';
        
        html += `
            <div class="d-flex justify-content-between">
                <span>${item.name} x ${item.quantity}${serviceTimeText}</span>
                <span>৳${itemTotal}</span>
            </div>
        `;
    });
    
    orderSummary.innerHTML = html;
    orderTotal.textContent = total.toFixed(2);
    
    bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

// Initialize cart system
document.addEventListener('DOMContentLoaded', function() {
    window.cart = new SimpleCart();
});

// Add to cart function for onclick
function addToCart(productId, productName, productPrice, productImage, categoryType = 'product') {
    if (window.cart) {
        window.cart.addToCart(productId, productName, productPrice, productImage, categoryType);
    }
}
</script>