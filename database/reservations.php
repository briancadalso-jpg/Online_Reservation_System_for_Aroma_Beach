<?php

require_once __DIR__ . '/../src/includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$action = $_POST['action'] ?? '';
$reservationModel = new Reservation();

if ($action === 'create') {
    if (is_admin()) {
        json_response(['success' => false, 'message' => 'Admin accounts cannot create guest reservations from this form.'], 403);
    }

    $cottageId = (int) ($_POST['cot_id'] ?? 0);
    $startDatetime = trim($_POST['start_datetime'] ?? '');
    $endDatetime = trim($_POST['end_datetime'] ?? '');
    $guests = (int) ($_POST['guests'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $guestDetails = [
        'guest_fname'   => trim($_POST['guest_fname'] ?? ''),
        'guest_lname'   => trim($_POST['guest_lname'] ?? ''),
        'guest_email'   => trim($_POST['guest_email'] ?? ''),
        'guest_phone'   => trim($_POST['guest_phone'] ?? ''),
        'guest_address' => trim($_POST['guest_address'] ?? ''),
    ];

    if ($cottageId <= 0 || $startDatetime === '' || $endDatetime === '' || $guests <= 0) {
        json_response(['success' => false, 'message' => 'Please complete the reservation form.'], 422);
    }

    foreach ($guestDetails as $value) {
        if ($value === '') {
            json_response(['success' => false, 'message' => 'Please provide the guest information requested in the pop-up modal.'], 422);
        }
    }

    if (!filter_var($guestDetails['guest_email'], FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'Please provide a valid guest email address.'], 422);
    }

    $start = strtotime($startDatetime);
    $end = strtotime($endDatetime);

    if (!$start || !$end || $end <= $start) {
        json_response(['success' => false, 'message' => 'End date/time must be later than the start date/time.'], 422);
    }

    if ($start < time()) {
        json_response(['success' => false, 'message' => 'Start date/time must not be in the past.'], 422);
    }

    $cottageModel = new Cottage();
    $cottage = $cottageModel->getById($cottageId);

    if (!$cottage) {
        json_response(['success' => false, 'message' => 'Selected cottage was not found.'], 404);
    }

    if ($guests > (int) $cottage['cot_capacity']) {
        json_response(['success' => false, 'message' => 'Guest count exceeds the cottage capacity.'], 422);
    }

    $totals = calculate_totals((float) $cottage['cot_price'], date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end));
    $result = $reservationModel->create(
        null,
        $cottageId,
        date('Y-m-d H:i:s', $start),
        date('Y-m-d H:i:s', $end),
        $guests,
        $notes,
        $totals['base_amount'],
        $totals['vat_amount'],
        $totals['processing_fee'],
        $totals['total_amount'],
        $guestDetails
    );

    if ($result['success'] ?? false) {
        $result['receipt_url'] = route_for_section('receipt.php', 'users') . '?res_id=' . (int) $result['res_id'];
    }

    json_response($result, $result['success'] ? 200 : 422);
}

if ($action === 'update' && is_admin()) {
    $reservationId = (int) ($_POST['res_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $amountPaid = (float) ($_POST['amount_paid'] ?? 0);

    if ($reservationId <= 0 || !in_array($status, ['pending', 'approved', 'cancelled', 'expired'], true) || $amountPaid < 0) {
        json_response(['success' => false, 'message' => 'Please provide valid reservation update details.'], 422);
    }

    $admin = new Admin();
    $result = $admin->updateReservation($reservationId, $status, $amountPaid);
    json_response($result, $result['success'] ? 200 : 422);
}

if ($action === 'delete' && is_admin()) {
    $reservationId = (int) ($_POST['res_id'] ?? 0);

    if ($reservationId <= 0) {
        json_response(['success' => false, 'message' => 'Invalid reservation ID.'], 422);
    }

    $admin = new Admin();
    $result = $admin->deleteReservation($reservationId);
    json_response($result, $result['success'] ? 200 : 422);
}

json_response(['success' => false, 'message' => 'Unknown or unauthorized action.'], 400);
