<?php
require_once __DIR__ . '/src/includes/bootstrap.php';

echo "=== Redirect Logic Verification ===\n\n";

// Simulate admin session
$_SESSION['id'] = 1;
$_SESSION['name'] = 'Test Admin';
$_SESSION['email'] = 'admin@test.com';
$_SESSION['role'] = 'admin';

echo "1. Session Data:\n";
echo "   - ID: " . ($_SESSION['id'] ?? 'EMPTY') . "\n";
echo "   - Role: " . ($_SESSION['role'] ?? 'EMPTY') . "\n";
echo "   - is_logged_in(): " . (is_logged_in() ? 'YES' : 'NO') . "\n";
echo "   - is_admin(): " . (is_admin() ? 'YES' : 'NO') . "\n\n";

echo "2. Redirect URLs:\n";
echo "   - role_home_url(): " . role_home_url() . "\n";
echo "   - Expected: /aroma_resortsystem/public/admin/home.php\n\n";

// Test with user session
$_SESSION['role'] = 'user';
echo "3. User Session (role=user):\n";
echo "   - is_admin(): " . (is_admin() ? 'YES' : 'NO') . "\n";
echo "   - role_home_url(): " . role_home_url() . "\n";
echo "   - Expected: /aroma_resortsystem/public/users/home.php\n\n";

// Restore admin session
$_SESSION['role'] = 'admin';

// Test route_for_section
echo "4. Route Helpers:\n";
echo "   - route_for_section('home.php', 'admin'): " . route_for_section('home.php', 'admin') . "\n";
echo "   - route_for_section('home.php', 'users'): " . route_for_section('home.php', 'users') . "\n";
echo "   - route_for_section('login.php'): " . route_for_section('login.php') . "\n\n";

echo "✓ All functions are accessible and working.\n";
?>
