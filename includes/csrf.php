<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log("CSRF Debug: Generated NEW token: " . $_SESSION['csrf_token'] . " for Session: " . session_id());
}

function csrf_token() {
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf_token() {
    error_log("CSRF Debug: Verifying. Session ID: " . session_id());
    error_log("CSRF Debug: Session Token: " . (isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : 'NULL'));
    error_log("CSRF Debug: POST Token:    " . (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : 'NULL'));

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

