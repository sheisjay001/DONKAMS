<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf_token() {
    if (!isset($_POST['csrf_token'])) {
        die("CSRF Token Validation Failed: Token missing from form submission.");
    }
    if (!isset($_SESSION['csrf_token'])) {
        die("CSRF Token Validation Failed: Token missing from session. Please refresh the page and try again.");
    }
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed: Token mismatch.");
    }
}

