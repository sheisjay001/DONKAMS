<?php
session_start();
require_once 'config/db.php';
require_once 'includes/csrf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF
verify_csrf_token();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to place an order']);
    exit;
}

// Check cart
if (empty($_SESSION['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
    exit;
}

$user_id = $_SESSION['user_id'];
$fullname = $_POST['fullname'] ?? '';
$address = $_POST['address'] ?? '';
$phone = $_POST['phone'] ?? '';
$note = $_POST['note'] ?? '';

if (empty($fullname) || empty($address) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields']);
    exit;
}

// Calculate total
$total_price = 0;
$cart_items = [];
$ids = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT * FROM products WHERE id IN ($ids)";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['qty'] = $qty;
        $row['subtotal'] = $row['price'] * $qty;
        $total_price += $row['subtotal'];
        $cart_items[] = $row;
    }
}

// Insert Order
// Assumes table 'orders' exists with user_id, total_amount, status, created_at
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("id", $user_id, $total_price);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    
    // Insert Order Items
    // Assumes table 'order_items' exists with order_id, product_id, quantity, price
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
        $item_stmt->execute();
    }
    
    // Clear Cart
    unset($_SESSION['cart']);
    
    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully', 'order_id' => $order_id]);
} else {
    // Log error if needed
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order: ' . $conn->error]);
}
?>