<?php
require_once 'config/db.php';
session_start();
include 'includes/header.php';

$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['qty'] = $_SESSION['cart'][$row['id']];
            $row['subtotal'] = $row['price'] * $row['qty'];
            $total_price += $row['subtotal'];
            $cart_items[] = $row;
        }
    }
}
?>

<main class="container cart-page" style="padding: 2rem 0;">
    <h2 class="section-title">Your Shopping Cart</h2>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart" style="text-align: center; padding: 3rem;">
            <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p>Your cart is currently empty.</p>
            <a href="index.php" class="btn" style="margin-top: 1rem;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr data-id="<?php echo $item['id']; ?>">
                        <td class="product-col">
                            <img src="images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-img" onerror="this.src='https://via.placeholder.com/50'">
                            <span><?php echo $item['name']; ?></span>
                        </td>
                        <td>₦<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="qty-control">
                                <button class="qty-btn minus" onclick="updateCart(<?php echo $item['id']; ?>, <?php echo $item['qty'] - 1; ?>)">-</button>
                                <input type="number" value="<?php echo $item['qty']; ?>" min="1" onchange="updateCart(<?php echo $item['id']; ?>, this.value)">
                                <button class="qty-btn plus" onclick="updateCart(<?php echo $item['id']; ?>, <?php echo $item['qty'] + 1; ?>)">+</button>
                            </div>
                        </td>
                        <td>₦<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <button class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-summary">
                <h3>Cart Total</h3>
                <p class="total-amount">₦<?php echo number_format($total_price, 2); ?></p>
                <div class="cart-actions">
                    <button onclick="clearCart()" class="btn btn-outline">Clear Cart</button>
                    <a href="checkout.php" class="btn">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
function updateCart(id, qty) {
    if (qty < 1) return;
    fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=update&product_id=${id}&quantity=${qty}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            // Update row subtotal
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const priceText = row.children[1].innerText.replace('₦', '').replace(/,/g, '');
                const price = parseFloat(priceText);
                const subtotal = price * qty;
                row.children[3].innerText = '₦' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                // Update input and buttons
                const input = row.querySelector('input');
                if (input) input.value = qty;
                
                const minusBtn = row.querySelector('.minus');
                const plusBtn = row.querySelector('.plus');
                if (minusBtn) minusBtn.setAttribute('onclick', `updateCart(${id}, ${qty - 1})`);
                if (plusBtn) plusBtn.setAttribute('onclick', `updateCart(${id}, ${qty + 1})`);
            }
            
            // Update global total
            const totalEl = document.querySelector('.total-amount');
            if (totalEl && data.formatted_total) {
                totalEl.innerText = '₦' + data.formatted_total;
            }

            // Update cart count
            const cartCount = document.getElementById('cart-count');
            if(cartCount) cartCount.innerText = data.cart_count;
            
            // Optional: Show toast if available
            if(typeof showToast === 'function') showToast('Cart Updated', 'Quantity updated successfully');
        }
    });
}

function removeFromCart(id) {
    if(!confirm('Remove this item?')) return;
    
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if(row) {
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
    }

    fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=remove&product_id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            if (document.querySelectorAll('tbody tr').length <= 1) {
                location.reload();
            } else {
                if(row) row.remove();
                
                const cartCount = document.getElementById('cart-count');
                if(cartCount) cartCount.innerText = data.cart_count;
                
                const totalEl = document.querySelector('.total-amount');
                if (totalEl && data.formatted_total) {
                    totalEl.innerText = '₦' + data.formatted_total;
                }
                
                if(typeof showToast === 'function') showToast('Item Removed', 'Product removed from cart');
            }
        }
    });
}

function clearCart() {
    if(!confirm('Clear entire cart?')) return;
    fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=clear`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') location.reload();
    });
}
</script>

<?php include 'includes/footer.php'; ?>
