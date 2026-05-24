<?php

require_once __DIR__ . '/../src/includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$action = $_GET['action'] ?? '';
$client = new Client();

if ($action === 'login') {
    $email = trim($_POST['admin_email'] ?? ''); // Changed to admin_email
    $password = $_POST['admin_pass'] ?? '';     // Changed to admin_pass

    if ($email === '' || $password === '') {
        json_response(['success' => false, 'message' => 'Email and password are required.'], 422);
    }

    $result = $client->login($email, $password);

    if ($result['success'] ?? false) {
        $result['redirect_url'] = role_home_url();
    }

    json_response($result, ($result['success'] ?? false) ? 200 : 422);
}

if ($action === 'signup') {
    $required = ['admin_fname', 'admin_lname', 'admin_phone', 'admin_address', 'admin_email', 'admin_pass']; // Updated required fields

    foreach ($required as $field) {
        if (trim($_POST[$field] ?? '') === '') {
            json_response(['success' => false, 'message' => 'Please complete all required fields.'], 422);
        }
    }
    $result = $client->signUp(
        trim($_POST['admin_fname']),   // Changed to admin_fname
        trim($_POST['admin_mname'] ?? ''), // Changed to admin_mname
        trim($_POST['admin_lname']),   // Changed to admin_lname
        trim($_POST['admin_phone']),   // Changed to admin_phone
        trim($_POST['admin_address']), // Changed to admin_address
        trim($_POST['admin_email']),   // Changed to admin_email
        $_POST['admin_pass']           // Changed to admin_pass
    );

    json_response($result, $result['success'] ? 200 : 422);
}

json_response(['success' => false, 'message' => 'Unknown action.'], 400);
