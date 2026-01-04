<?php
// Use Cookie-based CSRF to handle stateless environments (like Vercel) better than file-based sessions
// The "Double Submit Cookie" pattern.

function ensure_csrf_cookie() {
    if (!isset($_COOKIE['csrf_token'])) {
        try {
            $token = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $token = md5(uniqid(rand(), true));
        }
        
        // Determine if we are on HTTPS
        $secure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $secure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            $secure = true;
        }

        // Set cookie: name, value, expire, path, domain, secure, httponly
        // Expires in 2 hours
        setcookie('csrf_token', $token, [
            'expires' => time() + 7200,
            'path' => '/',
            'secure' => $secure, 
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        // Make available immediately in this script execution
        $_COOKIE['csrf_token'] = $token;
    }
}

// Call immediately to set header
ensure_csrf_cookie();

function csrf_token() {
    return isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : '';
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf_token() {
    if (!isset($_POST['csrf_token'])) {
        die("CSRF Error: Form token missing.");
    }
    if (!isset($_COOKIE['csrf_token'])) {
        die("CSRF Error: Security cookie missing. Please enable cookies and refresh the page.");
    }
    if ($_POST['csrf_token'] !== $_COOKIE['csrf_token']) {
        die("CSRF Error: Token mismatch. Please refresh the page and try again.");
    }
}

