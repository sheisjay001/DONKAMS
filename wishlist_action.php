<?php
require_once 'config/db.php';
session_start();

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $response = ['status' => 'error', 'message' => 'Login required'];
    } else {
        $user_id = (int)$_SESSION['user_id'];
        $action = $_POST['action'] ?? '';
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $wishlist_id = isset($_POST['wishlist_id']) ? (int)$_POST['wishlist_id'] : 0;

        if ($action === 'add' && $product_id > 0) {
            $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
            $check->bind_param("ii", $user_id, $product_id);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            if (!$exists) {
                $ins = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
                $ins->bind_param("ii", $user_id, $product_id);
                $ins->execute();
            }
            $count_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM wishlist WHERE user_id = ?");
            $count_stmt->bind_param("i", $user_id);
            $count_stmt->execute();
            $cnt = $count_stmt->get_result()->fetch_assoc()['cnt'] ?? 0;
            $response = ['status' => 'success', 'message' => 'Added to wishlist', 'wishlist_count' => (int)$cnt];
        } elseif ($action === 'remove') {
            if ($wishlist_id > 0) {
                $del = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
                $del->bind_param("ii", $wishlist_id, $user_id);
                $del->execute();
            } elseif ($product_id > 0) {
                $del = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                $del->bind_param("ii", $user_id, $product_id);
                $del->execute();
            }
            $count_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM wishlist WHERE user_id = ?");
            $count_stmt->bind_param("i", $user_id);
            $count_stmt->execute();
            $cnt = $count_stmt->get_result()->fetch_assoc()['cnt'] ?? 0;
            $response = ['status' => 'success', 'message' => 'Removed from wishlist', 'wishlist_count' => (int)$cnt];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
