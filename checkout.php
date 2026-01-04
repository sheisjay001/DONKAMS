<?php
require_once 'config/db.php';
session_start();
require_once 'includes/csrf.php';
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$cart_items = [];
$total_price = 0;
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
?>

<main class="container" style="padding: 2rem 5%; max-width: 800px; margin: 0 auto;">
    <h2 class="section-title">Checkout</h2>

    <div class="auth-card" style="max-width: 100%; text-align: left; padding: 2rem;">
        <h3>Order Summary</h3>
        <ul style="list-style: none; margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
            <?php foreach ($cart_items as $item): ?>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span><?php echo $item['qty']; ?>x <?php echo $item['name']; ?></span>
                    <span>₦<?php echo number_format($item['subtotal'], 2); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; margin-bottom: 2rem;">
            <span>Total:</span>
            <span style="color: var(--secondary-color);">₦<?php echo number_format($total_price, 2); ?></span>
        </div>

        <h3 style="margin-bottom: 1rem;">Shipping Information</h3>
        <form id="checkout-form" onsubmit="processCheckout(event)">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label>Delivery Address</label>
                <input type="text" id="address" name="address" required placeholder="Enter your delivery address">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
            </div>
            <div class="form-group">
                <label>Note (Optional)</label>
                <textarea id="note" name="note" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" placeholder="Any special instructions?"></textarea>
            </div>
            
            <button type="submit" class="btn btn-full" style="background-color: #25D366; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fab fa-whatsapp" style="font-size: 1.2rem;"></i> Place Order & Chat on WhatsApp
            </button>
        </form>
    </div>
</main>

<script>
function processCheckout(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const form = document.getElementById('checkout-form');
    const formData = new FormData(form);

    fetch('place_order.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Construct WhatsApp Message
            const fullname = document.getElementById('fullname').value;
            const address = document.getElementById('address').value;
            const phone = document.getElementById('phone').value;
            const note = document.getElementById('note').value;
            const orderId = data.order_id;
            
            let message = `*New Order #${orderId} from DONKAMS Website* 🛒%0A%0A`;
            message += `*Customer Details:*%0A`;
            message += `👤 Name: ${fullname}%0A`;
            message += `📍 Address: ${address}%0A`;
            message += `📞 Phone: ${phone}%0A%0A`;
            
            message += `*Order Items:*%0A`;
            <?php foreach ($cart_items as $item): ?>
            message += `• <?php echo $item['qty']; ?>x <?php echo $item['name']; ?> - ₦<?php echo number_format($item['subtotal'], 2); ?>%0A`;
            <?php endforeach; ?>
            
            message += `%0A*💰 Total Amount: ₦<?php echo number_format($total_price, 2); ?>*%0A`;
            
            if(note) {
                message += `%0A📝 Note: ${note}`;
            }

            // WhatsApp Number (from footer contact)
            const phoneNumber = "2349031518307";
            
            // Create WhatsApp URL
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${message}`;
            
            // Open WhatsApp
            window.open(whatsappUrl, '_blank');
            
            // Redirect to account page
            window.location.href = 'account.php';
        } else {
            alert(data.message || 'An error occurred');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>

<?php include 'includes/footer.php'; ?>
