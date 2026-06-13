<?php
require_once __DIR__ . '/session.php';

$title = $title ?? ($pageTitle ?? 'Aroma Beach Resort');
$activePage = basename($_SERVER['PHP_SELF'] ?? '');
$loggedIn = is_logged_in();
$authPage = in_array($activePage, ['login.php', 'signup.php'], true);

$bodyClass = [];
if (!$authPage) {
    $bodyClass[] = 'bg-gray-100';
}
if ($activePage === 'signup.php') {
    $bodyClass[] = 'auth-signup-page';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?> | Aroma Beach Resort</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <?php if ($authPage): ?>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                opacity: 1;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                color: #172026;
                background:
                    radial-gradient(circle at top right, rgba(249,115,22,.18), transparent 24%),
                    linear-gradient(rgba(255,249,239,.86), rgba(255,249,239,.94)),
                    url('<?php echo e(frontend_asset('campuyo1.jpg')); ?>') center/cover fixed;
            }
            .page { min-height:100vh; padding:36px 20px; display:flex; align-items:center; justify-content:center; }
        </style>
    <?php else: ?>
        <style>
            body { opacity: 0; }
            body.page-ready { opacity: 1; }
            .page-transition-stage {
                animation: fadeIn 420ms cubic-bezier(.2,.8,.2,1) both;
                transition: opacity 260ms ease;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(18px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    <?php endif; ?>
</head>

<body
    class="<?php echo e(implode(' ', $bodyClass)); ?>"
    data-login-url="<?php echo e(route_for_section('login.php')); ?>"
    data-signup-url="<?php echo e(route_for_section('signup.php')); ?>"
    data-home-url="<?php echo e(route_for_section('home.php')); ?>"
    data-cottages-url="<?php echo e(route_for_section('cottages.php')); ?>"
    data-reservations-url="<?php echo e(route_for_section('reservations.php')); ?>"
    data-logout-url="<?php echo e(route_for_section('logout.php')); ?>"
    data-auth-api-url="<?php echo e(route_url('controller/auth.php')); ?>"
    data-cottages-api-url="<?php echo e(route_url('controller/cottages.php')); ?>"
    data-reservations-api-url="<?php echo e(route_url('controller/reservations.php')); ?>"
    data-section="<?php echo e(current_section()); ?>"
>

<?php if ($authPage): ?>
    <main class="page page-transition-stage">
        <div class="auth-shell">
<?php else: ?>
    <nav class="bg-green-800 border-gray-200 relative">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="<?php echo e(route_for_section('home.php')); ?>" class="flex items-center space-x-3">
                <img src="<?php echo e(frontend_asset('aromaresortlogo2.png')); ?>" class="h-10 w-auto" alt="Aroma Logo" />
                <span class="hidden md:block self-center text-xl font-bold whitespace-nowrap text-white">Aroma Beach Resort</span>
            </a>
            <div class="flex items-center">
                <?php if (is_admin()): ?>
                    <button id="adminAccountButton" type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="adminDropdown">
                        <span class="sr-only">Open user menu</span>
                        <div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                            <svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                    </button>

                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="adminDropdown">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white"><?php echo e(current_user_name()); ?></span>
                            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400"><?php echo e($_SESSION['admin_email'] ?? ''); ?></span>
                        </div>
                        <ul class="py-2" aria-labelledby="adminAccountButton">
                            <li>
                                <a href="<?php echo e(route_for_section('cottages.php')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Cottages</a>
                            </li>
                            <li>
                                <a href="<?php echo e(route_for_section('reservations.php')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Reservations</a>
                            </li>
                            <li>
                                <a href="<?php echo e(route_for_section('logout.php')); ?>" data-logout-link="true" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Logout</a>
                            </li>
                        </ul>
                    </div>
                <?php elseif ($loggedIn): ?>
                    <a href="<?php echo e(route_for_section('reservations.php')); ?>" class="text-sm font-semibold text-white hover:underline mr-4">My Reservations</a>
                    <a href="<?php echo e(route_for_section('logout.php')); ?>" class="text-sm font-semibold text-red-200 hover:underline">Logout</a>
                <?php else: ?>
                   
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="min-h-screen page-transition-stage">
<?php endif; ?>
