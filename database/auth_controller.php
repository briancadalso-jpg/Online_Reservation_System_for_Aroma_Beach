<?php

declare(strict_types=1);

require_once __DIR__ . '/system_core.php';

function login_user(string $email, string $role = 'user'): void
{
    $_SESSION['user'] = ['email' => $email];
    $_SESSION['role'] = $role;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            (bool)$params['secure'], (bool)$params['httponly']
        );
    }

    session_destroy();
}

