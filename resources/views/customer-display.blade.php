<!DOCTYPE html>
<html>
<head>
    <title>Customer Display</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e2e2;
        }
        .header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
            color: #222;
        }
        .header-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 8px;
            font-size: 15px;
            color: #777;
            font-weight: 500;
        }
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-bottom: 180px; /* Space for sticky total */
        }
        .cart-table thead tr th {
            text-align: left;
            padding: 14px 20px;
            background-color: #fafafa;
            font-weight: 600;
            color: #555;
            border-bottom: 3px solid #ddd;
            border-radius: 8px 8px 0 0;
        }
        .cart-table tbody tr {
            background-color: #fff;
            box-shadow: 0 2px 7px rgba(0,0,0,0.05);
            border-radius: 10px;
            transition: background-color 0.2s ease;
        }
        .cart-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        .cart-table tbody tr td {
            padding: 18px 20px;
            vertical-align: middle;
            border: none;
            font-size: 16px;
            color: #444;
        }
        .cart-table tbody tr td img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .currency-symbol {
            font-family: 'Arial', sans-serif;
            font-size: 0.85em;
            font-weight: 600;
            color: #666;
            margin-right: 5px;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 700;
            font-size: 17px;
            color: #222;
            margin-bottom: 6px;
        }
        .product-details {
            color: #888;
            font-size: 14px;
            font-weight: 500;
        }
        .text-right {
            text-align: right;
            font-weight: 600;
            color: #222;
            font-size: 16px;
        }
        
        /* Sticky total section styles */
        .total-section-container {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px 0;
            border-top: 3px solid #ddd;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            margin-top: auto; /* Push to bottom */
        }
        
        .total-section {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 30px;
        }
        
        .total-row {
            font-size: 18px;
            font-weight: 600;
            color: #555;
            min-width: 200px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 10px;
        }
        .grand-total {
            font-size: 22px;
            font-weight: 700;
            color: #111;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
        }
        .discount {
            color: #e74c3c;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 0;
            color: #999;
            font-size: 20px;
            font-weight: 600;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        /* Scrollable area for cart items */
        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Order</h1>
            <div class="header-info">
                <span id="order-date">${new Date().toLocaleDateString()}</span>
                <span id="order-time">${new Date().toLocaleTimeString()}</span>
            </div>
        </div>
        
        <div class="cart-items-container">
            <div id="cart-content">
                <table class="cart-table" id="cart-items">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item</th>
                            <th>Takeaway Cost</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="empty-cart">No items in cart</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="total-section-container">
            <div id="cart-total" class="total-section" style="display: none;">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span id="subtotal-amount">0.00</span>
                </div>
                <div class="total-row discount">
                    <span>Discount</span>
                    <span id="discount-amount">0.00</span>
                </div>
                <div class="total-row">
                    <span>Tax</span>
                    <span id="tax-amount">0.00</span>
                </div>
                <div class="total-row">
                    <span>Take Away</span>
                    <span id="shipping-amount">0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total</span>
                    <span id="total-amount">0.00</span>
                </div>
            </div>
        </div>
    </div>

    <script>
// Listen for localStorage changes
window.addEventListener('storage', function(event) {
    if (event.key === 'customer_display_reload') {
        window.location.reload();
    }
});

        // Listen for storage changes (cross-tab communication)
window.addEventListener('storage', function(event) {
    if (event.key === 'customer_display_reload') {
        window.location.reload();
    }
});

        // Update time every second
        function updateDateTime() {
            const now = new Date();
            document.getElementById('order-date').textContent = now.toLocaleDateString();
            document.getElementById('order-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Listen for messages from the parent window
        window.addEventListener('message', function(event) {
            if (event.data.type === 'update_cart') {
                updateCartDisplay(event.data.details, event.data.total);
            } else if (event.data.type === 'reset_display') {
                resetCartDisplay();
            } else if (event.data.type === 'update_total') {
                updateTotalAmount(event.data.total, event.data.discount, event.data.tax, event.data.shipping);
            }
        });

        // Function to update all amounts
        function updateTotalAmount(total, discount = 0, tax = 0, shipping = 0) {
            const subtotal = total + discount - tax - shipping;
            
            // Format the currency based on symbol placement
            const symbol = 'Rs';
            const symbolBefore = true;
            
            document.getElementById('subtotal-amount').innerHTML = formatCurrency(subtotal, symbol, symbolBefore);
            document.getElementById('discount-amount').innerHTML = formatCurrency(discount, symbol, symbolBefore);
            document.getElementById('tax-amount').innerHTML = formatCurrency(tax, symbol, symbolBefore);
            document.getElementById('shipping-amount').innerHTML = formatCurrency(shipping, symbol, symbolBefore);
            document.getElementById('total-amount').innerHTML = formatCurrency(total, symbol, symbolBefore);
            
            // Show the total section
            document.getElementById('cart-total').style.display = 'block';
        }

        // Function to format currency with symbol
        function formatCurrency(amount, symbol, symbolBefore) {
            const formattedAmount = parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2
            });
            
            return symbolBefore 
                ? `<span class="currency-symbol">${symbol}</span>${formattedAmount}`
                : `${formattedAmount}<span class="currency-symbol">${symbol}</span>`;
        }

        // Function to update cart display
        function updateCartDisplay(details, total) {
            const cartItems = document.querySelector('#cart-items tbody');
            const cartTotal = document.getElementById('cart-total');
            
            if (details.length === 0) {
                cartItems.innerHTML = '<tr><td colspan="4" class="empty-cart">No items in cart</td></tr>';
                cartTotal.style.display = 'none';
                return;
            }

            let itemsHtml = '';
            let subtotal = 0;
            
            details.forEach(item => {
                subtotal += item.subtotal;
                itemsHtml += `
                    <tr>
                        <td>
                            <img src="/images/products/${item.image}" alt="${item.name}">
                        </td>
                        <td>
                            <div class="product-name">${item.name}</div>
                            <div class="product-details">Qty: ${item.quantity}</div>
                        </td>
                        <td>
                            <div class="product-details"><span class="currency-symbol">Rs</span>${item.takeaway}</div>
                        </td>
                        <td class="text-right">
                            <span class="currency-symbol">Rs</span>${formatCurrency(item.subtotal, 'Rs', true)}
                        </td>
                    </tr>
                `;
            });

            cartItems.innerHTML = itemsHtml;
            cartTotal.style.display = 'block';
        }

        function resetCartDisplay() {
            const cartItems = document.querySelector('#cart-items tbody');
            const cartTotal = document.getElementById('cart-total');
            
            cartItems.innerHTML = '<tr><td colspan="4" class="empty-cart">No items in cart</td></tr>';
            cartTotal.style.display = 'none';
        }
    </script>
</body>
</html>