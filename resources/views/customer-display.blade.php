<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fdfdfdff 0%, #fdfeffff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        
        .order-container {
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            height: 95vh;
        }
        
        .header {
            background: #4a6fc3;
            color: white;
            padding: 20px 30px;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .order-id {
            background: #3a5bb0;
            padding: 6px 15px;
            border-radius: 20px;
            margin-top: 10px;
            display: inline-block;
            font-weight: 500;
        }
        
        .content-area {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        .items-section {
            flex: 7;
            padding: 25px;
            overflow-y: auto;
            background: #f9fafc;
        }
        
        .summary-section {
            flex: 3;
            background: white;
            border-left: 1px solid #e1e5eb;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            padding-bottom: 10px;
            border-bottom: 2px solid #e1e5eb;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cart-table th {
            text-align: left;
            padding: 15px 10px;
            background-color: #f1f5f9;
            color: #4a5568;
            font-weight: 600;
        }
        
        .cart-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #e1e5eb;
        }
        
        .cart-item {
            transition: background-color 0.2s;
        }
        
        .cart-item:hover {
            background-color: #f1f7ff;
        }
        
        .item-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e1e5eb;
        }
        
        .item-details {
            padding-left: 15px;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 16px;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .item-meta {
            color: #718096;
            font-size: 14px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .price {
            font-weight: 600;
            color: #2d3748;
        }
        
        .summary-card {
            background: #f8fafc;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 16px;
        }
        
        .grand-total {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            border-top: 2px dashed #cbd5e0;
            padding-top: 15px;
            margin-top: 10px;
        }
        
        .discount {
            color: #e53e3e;
        }
        
        .payment-summary {
            margin-top: auto;
            background: #f0f7ff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 16px;
        }
        
        .due-amount {
            color: #e53e3e;
            font-weight: 700;
        }
        
        .balance-amount {
            color: #38a169;
            font-weight: 700;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            color: #a0aec0;
        }
        
        .empty-cart i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #cbd5e0;
        }
        
        .empty-cart p {
            font-size: 18px;
            margin-top: 10px;
        }
        
        .currency {
            font-weight: 600;
            color: #4a5568;
        }
        
        @media (max-width: 768px) {
            .content-area {
                flex-direction: column;
            }
            
            .summary-section {
                border-left: none;
                border-top: 1px solid #e1e5eb;
            }
            
            .order-container {
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="order-container">
        <!-- Header -->
        <div class="header">
            <h1>Your Order</h1>
            <div class="header-info">
                <span id="order-date">${new Date().toLocaleDateString()}</span>
                <span id="order-time">${new Date().toLocaleTimeString()}</span>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="content-area">
            <!-- Items Section -->
            <div class="items-section">
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
                            <td colspan="4" class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <p>No items in cart</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="summary-section">
                <div class="section-title">Order Summary</div>

                <!-- Cart Totals -->
                <div id="cart-total" class="summary-card" style="display: none;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal-amount">0.00</span>
                    </div>
                    <div class="summary-row discount">
                        <span>Discount</span>
                        <span id="discount-amount">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span id="tax-amount">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Take Away</span>
                        <span id="shipping-amount">0.00</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Total</span>
                        <span id="total-amount">0.00</span>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div id="payment-summary" class="payment-summary">
                    <div class="payment-row">
                        <span>Amount Given</span>
                        <span id="customer-given">Rs 0.00</span>
                    </div>
                    <div class="payment-row" id="balance-row">
                        <span>Balance Return</span>
                        <span id="balance-return">Rs 0.00</span>
                    </div>
                    <div class="payment-row due-amount" id="due-row" style="display: none;">
                        <span>Amount Due</span>
                        <span id="amount-due">Rs 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCustomerPaymentSummary(data) {
            const symbol = data.currency || 'Rs';
            const symbolBefore = data.symbol_placement === 'before';
            const given = parseFloat(data.customerGivenAmount || 0);
            const total = parseFloat(data.grandTotal || 0);
            const balance = parseFloat(data.balanceToReturn || 0);

            document.getElementById('customer-given').innerHTML = formatCurrency(given, symbol, symbolBefore);

            if (balance >= 0) {
                document.getElementById('balance-return').innerHTML = formatCurrency(balance, symbol, symbolBefore);
                document.getElementById('balance-row').style.display = 'flex';
                document.getElementById('due-row').style.display = 'none';
            } else {
                document.getElementById('amount-due').innerHTML = formatCurrency(Math.abs(balance), symbol, symbolBefore);
                document.getElementById('balance-row').style.display = 'none';
                document.getElementById('due-row').style.display = 'flex';
            }
        }

        // Listen for localStorage changes
        window.addEventListener('storage', function(event) {
            if (event.key === 'customer_display_reload') window.location.reload();
            if (event.key === 'customer_display_sync') {
                const paymentData = JSON.parse(event.newValue || '{}');
                updateCustomerPaymentSummary(paymentData);
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
            if (event.data.type === 'update_cart') updateCartDisplay(event.data.details, event.data.total);
            else if (event.data.type === 'reset_display') resetCartDisplay();
            else if (event.data.type === 'update_total') updateTotalAmount(event.data.total, event.data.discount, event.data.tax, event.data.shipping);
        });

        // Function to update all amounts
        function updateTotalAmount(total, discount = 0, tax = 0, shipping = 0) {
            const subtotal = total + discount - tax - shipping;
            const symbol = 'Rs';
            const symbolBefore = true;

            document.getElementById('subtotal-amount').innerHTML = formatCurrency(subtotal, symbol, symbolBefore);
            document.getElementById('discount-amount').innerHTML = formatCurrency(discount, symbol, symbolBefore);
            document.getElementById('tax-amount').innerHTML = formatCurrency(tax, symbol, symbolBefore);
            document.getElementById('shipping-amount').innerHTML = formatCurrency(shipping, symbol, symbolBefore);
            document.getElementById('total-amount').innerHTML = formatCurrency(total, symbol, symbolBefore);

            document.getElementById('cart-total').style.display = 'block';
        }

        function formatCurrency(amount, symbol, symbolBefore) {
            const formattedAmount = parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return symbolBefore ? `<span class="currency-symbol">${symbol}</span>${formattedAmount}` : `${formattedAmount}<span class="currency-symbol">${symbol}</span>`;
        }

        function updateCartDisplay(details, total) {
            const cartItems = document.querySelector('#cart-items tbody');
            const cartTotal = document.getElementById('cart-total');

            if (details.length === 0) {
                cartItems.innerHTML = '<tr><td colspan="4" class="empty-cart"><i class="fas fa-shopping-cart"></i><p>No items in cart</p></td></tr>';
                cartTotal.style.display = 'none';
                return;
            }

            let itemsHtml = '';
            details.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td><img src="/images/products/${item.image}" alt="${item.name}" class="item-image"></td>
                        <td>
                            <div class="item-name">${item.name}</div>
                            <div class="item-meta">Qty: ${item.quantity}</div>
                        </td>
                        <td><div class="item-meta"><span class="currency-symbol">Rs</span>${item.takeaway}</div></td>
                        <td class="text-right"><span class="currency-symbol"></span>${formatCurrency(item.subtotal, 'Rs', true)}</td>
                    </tr>
                `;
            });

            cartItems.innerHTML = itemsHtml;
            cartTotal.style.display = 'block';
        }

        function resetCartDisplay() {
            const cartItems = document.querySelector('#cart-items tbody');
            const cartTotal = document.getElementById('cart-total');

            cartItems.innerHTML = '<tr><td colspan="4" class="empty-cart"><i class="fas fa-shopping-cart"></i><p>No items in cart</p></td></tr>';
            cartTotal.style.display = 'none';
        }

        const initData = localStorage.getItem('customer_display_sync');
        if (initData) updateCustomerPaymentSummary(JSON.parse(initData));
    </script>
</body>

</html>  
