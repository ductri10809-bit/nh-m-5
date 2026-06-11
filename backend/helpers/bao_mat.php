<?php
/**
 * bao_mat.php - Ham bao mat
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function hashMatKhau(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function kiemTraMatKhau(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function taoCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function kiemTraCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
