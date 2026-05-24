<?php

require_once __DIR__ . '/../src/includes/session.php';

if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Please log in first.'], 401);
}

if (!is_admin()) {
    json_response(['success' => false, 'message' => 'Admin access only.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$action = $_POST['action'] ?? 'create';
$name = trim($_POST['cot_name'] ?? ''); // Changed to cot_name
$type = trim($_POST['cottage_type'] ?? '');
$price = (float) ($_POST['cot_price'] ?? 0); // Changed to cot_price
$description = trim($_POST['description'] ?? '');
$capacity = (int) ($_POST['cot_capacity'] ?? 0); // Changed to cot_capacity

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];

$uploadDir = dirname(__DIR__) . '/public/uploads/cottages';

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
    json_response(['success' => false, 'message' => 'Unable to prepare upload directory.'], 500);
}

$admin = new Admin();
$cottageModel = new Cottage();

if ($action === 'delete') {
    $cottageId = (int) ($_POST['cot_id'] ?? 0); // Changed to cot_id

    if ($cottageId <= 0) {
        json_response(['success' => false, 'message' => 'Invalid cottage ID.'], 422);
    }

    $existing = $cottageModel->getById($cottageId);
    if (!$existing) {
        json_response(['success' => false, 'message' => 'Cottage not found.'], 404);
    }

    $result = $admin->deleteCottage($cottageId);

    if ($result['success'] && !empty($existing['image_path'])) {
        $existingImage = dirname(__DIR__) . '/' . ltrim($existing['image_path'], '/');
        if (is_file($existingImage)) {
            @unlink($existingImage);
        }
    }

    json_response($result, $result['success'] ? 200 : 422);
}

if ($name === '' || $type === '' || $price <= 0 || $description === '' || $capacity <= 0) {
    json_response(['success' => false, 'message' => 'Please provide complete cottage details.'], 422);
}

$imagePath = null;
$hasUpload = !empty($_FILES['image']['name']);

if ($action === 'create' && (!$hasUpload || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)) {
    json_response(['success' => false, 'message' => 'Please upload a valid cottage image.'], 422);
}

if ($hasUpload) {
    if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        json_response(['success' => false, 'message' => 'Please upload a valid cottage image.'], 422);
    }

    $mimeType = mime_content_type($_FILES['image']['tmp_name']);
    if (!isset($allowedTypes[$mimeType])) {
        json_response(['success' => false, 'message' => 'Only JPG, PNG, WEBP, and GIF images are allowed.'], 422);
    }

    $fileName = 'cottage_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$mimeType];
    $targetPath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        json_response(['success' => false, 'message' => 'Failed to upload the image.'], 500);
    }

    $imagePath = 'public/uploads/cottages/' . $fileName;
}

if ($action === 'update') {
    $cottageId = (int) ($_POST['cot_id'] ?? 0); // Changed to cot_id

    if ($cottageId <= 0) {
        json_response(['success' => false, 'message' => 'Invalid cottage ID.'], 422);
    }

    $existing = $cottageModel->getById($cottageId);
    if (!$existing) {
        json_response(['success' => false, 'message' => 'Cottage not found.'], 404);
    }

    $result = $admin->updateCottage($cottageId, $name, $type, $price, $description, $capacity, $imagePath);

    if (!$result['success'] && $imagePath !== null) {
        $newImage = dirname(__DIR__) . '/' . ltrim($imagePath, '/');
        if (is_file($newImage)) {
            @unlink($newImage);
        }
    }

    if ($result['success'] && $imagePath !== null && !empty($existing['image_path'])) {
        $oldImage = dirname(__DIR__) . '/' . ltrim($existing['image_path'], '/');
        if (is_file($oldImage)) {
            @unlink($oldImage);
        }
    }

    json_response($result, $result['success'] ? 200 : 422);
}

if ($action !== 'create') {
    json_response(['success' => false, 'message' => 'Unknown cottage action.'], 400);
}

$result = $admin->addCottage(
    $name,
    $type,
    $price,
    $description,
    $capacity,
    (string) $imagePath
);

json_response($result, $result['success'] ? 200 : 422);
