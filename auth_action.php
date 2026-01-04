<?php
session_start();
require_once 'config/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'];

    if ($action == 'register') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Basic validation
        if ($password !== $confirm_password) {
            header("Location: register.php?error=Passwords do not match");
            exit();
        }

        // Check if email or username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: register.php?error=Username or Email already exists");
            exit();
        }
        $stmt->close();

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php?success=Registration successful! Please login.");
        } else {
            header("Location: register.php?error=Registration failed. Please try again.");
        }
        $stmt->close();

    } elseif ($action == 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password correct, start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '';
                    if (!empty($redirect) && filter_var($redirect, FILTER_SANITIZE_URL)) {
                        header("Location: " . $redirect);
                    } else {
                        header("Location: index.php");
                    }
                }
                exit();
            } else {
                header("Location: login.php?error=Invalid password");
            }
        } else {
            header("Location: login.php?error=User not found");
        }
        $stmt->close();
    }
}
$conn->close();
?>
