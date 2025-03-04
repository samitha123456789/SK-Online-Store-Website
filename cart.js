
        // Function to add item to cart
        function addToCart(productId, name, price, quantity, image) {
            fetch('php/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'add_to_cart': true,
                    'product_id': productId,
                    'name': name,
                    'price': price,
                    'quantity': quantity,
                    'image': image
                })
            })
            .then(response => response.text())
            .then(message => {
                alert(message);
                renderCart(); // Refresh cart display
            })
            .catch(error => console.error('Error:', error));
        }
    
        // Fetch cart items from PHP and render them
        function renderCart() {
            fetch('php/cart.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Cart Data:", data); // Debugging - Check the response
                    const cartItemsContainer = document.getElementById('cart-items');
                    cartItemsContainer.innerHTML = ''; // Clear existing items
                    let cartTotal = 0;
        
                    if (!data.cartItems || data.cartItems.length === 0) {
                        cartItemsContainer.innerHTML = '<tr><td colspan="6">Your cart is empty.</td></tr>';
                        return;
                    }
        
                    data.cartItems.forEach(item => {
                        const total = item.price * item.quantity;
                        cartTotal += total;
        
                        const itemRow = document.createElement('tr');
                        itemRow.innerHTML = `
                            <td><img src="${item.image}" alt="${item.name}" width="50"></td>
                            <td>${item.name}</td>
                            <td>RS ${parseFloat(item.price).toFixed(2)}</td>
                            <td>
                                <input type="number" value="${item.quantity}" min="1" data-id="${item.id}" class="quantity-input">
                            </td>
                            <td>RS ${total.toFixed(2)}</td>
                            <td><button class="remove-item" data-id="${item.id}">Remove</button></td>
                        `;
                        cartItemsContainer.appendChild(itemRow);
                    });
        
                    document.getElementById('cart-total').textContent = `RS ${cartTotal.toFixed(2)}`;
                })
                .catch(error => console.error('Error fetching cart:', error));
        }
    
        // Handle item quantity change
        function handleQuantityChange(event) {
            if (event.target.classList.contains('quantity-input')) {
                const itemId = event.target.dataset.id;
                const newQuantity = parseInt(event.target.value);
    
                fetch('php/cart.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'update_item': true,
                        'item_id': itemId,
                        'quantity': newQuantity
                    }),
                }).then(() => {
                    renderCart(); // Re-render the cart after the update
                });
            }
        }
    
        // Handle removing item from cart
        function removeItem(event) {
            if (event.target.classList.contains('remove-item')) {
                const itemId = event.target.dataset.id;
        
                fetch('php/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        'remove_item': itemId
                    })
                })
                .then(response => response.text())
                .then(() => {
                    renderCart(); // Ensure cart refreshes properly
                })
                .catch(error => console.error('Error:', error));
            }
        }
        
    
        // Initialize event listeners
        document.getElementById('cart-items').addEventListener('input', handleQuantityChange);
        document.getElementById('cart-items').addEventListener('click', removeItem);
    
        // Render cart when the page loads
        renderCart();
