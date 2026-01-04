<?php
require_once 'config/db.php';
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if ($action === 'add' && $product_id > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        $total_items = array_sum($_SESSION['cart']);
        $response = ['status' => 'success', 'message' => 'Product added to cart', 'cart_count' => $total_items];
    } 
    elseif ($action === 'remove' && $product_id > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        $total_items = array_sum($_SESSION['cart']);
        $response = ['status' => 'success', 'message' => 'Product removed from cart', 'cart_count' => $total_items];
    }
    elseif ($action === 'update' && $product_id > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        $total_items = array_sum($_SESSION['cart']);
        $response = ['status' => 'success', 'message' => 'Cart updated', 'cart_count' => $total_items];
    }
    elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        $response = ['status' => 'success', 'message' => 'Cart cleared', 'cart_count' => 0];
    }

    // Calculate new total price
    $total_price = 0;
    if (!empty($_SESSION['cart'])) {
        $ids = implode(',', array_keys($_SESSION['cart']));
        if (!empty($ids)) {
            $sql = "SELECT id, price FROM products WHERE id IN ($ids)";
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    if (isset($_SESSION['cart'][$row['id']])) {
                        $qty = $_SESSION['cart'][$row['id']];
                        $total_price += $row['price'] * $qty;
                    }
                }
            }
        }
    }
    
    $response['total_price'] = $total_price;
    $response['formatted_total'] = number_format($total_price, 2);
}

header('Content-Type: application/json');
echo json_encode($response);
