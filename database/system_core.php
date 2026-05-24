<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const VAT_RATE = 0.12;
const PROCESSING_FEE = 20.00;

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_web_root(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $root = preg_replace('#/(src|controller|public(?:/(admin|users))?)(/.*)?$#', '', $scriptName);
    if ($root === $scriptName) {
        $root = dirname($scriptName);
    }
    return rtrim($root ?: dirname($scriptName), '/');
}

function frontend_asset(string $relativePath): string
{
    return app_web_root() . '/assets/' . ltrim($relativePath, '/');
}

function route_url(string $path): string
{
    return app_web_root() . '/' . ltrim($path, '/');
}

function current_section(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (str_contains($scriptName, '/public/admin/')) return 'admin';
    if (str_contains($scriptName, '/public/users/')) return 'users';
    return is_admin() ? 'admin' : 'users';
}

function route_for_section(string $page, ?string $section = null): string
{
    $section = $section ?? current_section();
    if (in_array($page, ['login.php', 'signup.php'], true)) {
        return route_url('src/' . $page);
    }
    if ($page === 'logout.php') {
        return route_url('src/logout.php');
    }
    return route_url('public/' . $section . '/' . $page);
}

function current_user_id(): int
{
    return (int) ($_SESSION['admin_id'] ?? 0);
}

function current_user_name(): string
{
    return $_SESSION['admin_name'] ?? 'Guest';
}

function format_currency(float $amount): string
{
    return 'PHP ' . number_format($amount, 2);
}

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . route_for_section('login.php'));
        exit();
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        $target = is_logged_in() ? role_home_url() : route_for_section('login.php');
        header('Location: ' . $target);
        exit();
    }
}

function redirect_if_logged_in(): void
{
    if (is_logged_in()) {
        header('Location: ' . role_home_url());
        exit();
    }
}

function role_home_url(): string
{
    if (!is_logged_in()) {
        return route_url('/');
    }

    $role = $_SESSION['role'] ?? '';

    return match ($role) {
        'admin' => route_url('public/admin/home.php'),
        default => route_for_section('home.php', 'users'),
    };
}

function reservation_duration_days(string $startDatetime, string $endDatetime): float
{
    $start = strtotime($startDatetime);
    $end = strtotime($endDatetime);
    $seconds = max(0, $end - $start);
    return max(1, ceil($seconds / 86400));
}

function calculate_totals(float $cottagePrice, string $startDatetime, string $endDatetime): array
{
    $days = reservation_duration_days($startDatetime, $endDatetime);
    $baseAmount = $cottagePrice * $days;
    $vatAmount = round($baseAmount * VAT_RATE, 2);
    $processingFee = PROCESSING_FEE;
    $totalAmount = round($baseAmount + $vatAmount + $processingFee, 2);

    return [
        'days' => $days,
        'base_amount' => round($baseAmount, 2),
        'vat_amount' => $vatAmount,
        'processing_fee' => $processingFee,
        'total_amount' => $totalAmount,
    ];
}

function reservation_has_short_stay(array $reservation): bool
{
    $start = strtotime($reservation['start_datetime']);
    $end = strtotime($reservation['end_datetime']);
    return ($end - $start) <= 86400;
}
