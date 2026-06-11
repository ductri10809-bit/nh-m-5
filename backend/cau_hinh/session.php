<?php
/**
 * session.php - Khoi tao session
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        // scope the session cookie to the project path so cookies are sent for requests
        // under /luxurious-fashion-store when running on localhost/Apache
        'cookie_path' => '/luxurious-fashion-store',
    ]);
}

function layUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function datUserId(int $id): void
{
    $_SESSION['user_id'] = $id;
}

function xoaSession(): void
{
    session_destroy();
}
