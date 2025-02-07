<?php
function validateUsername($username) {
    if (empty(trim($username))) {
        return false;
    }
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function validatePassword($password) {
    if (empty(trim($password))) {
        return false;
    }
    return strlen($password) >= 8;
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}