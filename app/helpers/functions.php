<?php
// CSRF Token generation and validation
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Input sanitization
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Authentication helper
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Format points with suffix
function formatPoints($points) {
    if ($points >= 1000000) {
        return round($points / 1000000, 1) . 'M';
    } elseif ($points >= 1000) {
        return round($points / 1000, 1) . 'K';
    }
    return $points;
}

// Get level color class
function getLevelColorClass($level) {
    switch ($level) {
        case 'diamond': return 'text-blue-500';
        case 'gold': return 'text-yellow-500';
        case 'silver': return 'text-gray-400';
        default: return 'text-amber-700';
    }
}

// Format date
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// URL helper
function url($path = '') {
    $path = trim($path, '/');
    return APP_URL . ($path ? '/public/' . $path : '/public');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Password strength check
function isPasswordStrong($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password);
}
