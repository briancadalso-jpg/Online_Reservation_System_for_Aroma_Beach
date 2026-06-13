<?php

require_once 'Connection.php';

class Reservation extends Dbh
{
    public function create(
        ?int $adminId,
        int $cottageId,
        string $startDatetime,
        string $endDatetime,
        int $guests,
        string $notes,
        float $baseAmount,
        float $vatAmount,
        float $processingFee,
        float $totalAmount,
        array $guestDetails = []
    ): array {
        $db = $this->connect();

        $stmt = $db->prepare(
            'SELECT res_id
             FROM reservations
             WHERE cot_id = ?
               AND status IN ("pending", "approved")
               AND NOT (end_datetime <= ? OR start_datetime >= ?)
             LIMIT 1'
        );
        $stmt->bind_param('iss', $cottageId, $startDatetime, $endDatetime);
        $stmt->execute();

        if ($stmt->get_result()->fetch_assoc()) {
            return ['success' => false, 'message' => 'The selected cottage is already reserved during that schedule.'];
        }

        $guestStmt = $db->prepare('INSERT INTO guests (guest_fname, guest_lname, guest_email, guest_phone, guest_address) VALUES (?,?,?,?,?)');
        $guestStmt->bind_param(
            'sssss',
            $guestDetails['guest_fname'],
            $guestDetails['guest_lname'],
            $guestDetails['guest_email'],
            $guestDetails['guest_phone'],
            $guestDetails['guest_address']
        );

        if (!$guestStmt->execute()) {
            return ['success' => false, 'message' => 'Unable to save guest information.'];
        }
        $guestId = $guestStmt->insert_id;

        $insert = $db->prepare(
            'INSERT INTO reservations
             (admin_id, guest_id, cot_id, start_datetime, end_datetime, guests, notes, base_amount, vat_amount, processing_fee, total_amount, amount_paid, pay_change, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?, 0, 0, "pending")'
        );
        $insert->bind_param(
            'iiissisdddd',
            $adminId,
            $guestId,
            $cottageId,
            $startDatetime,
            $endDatetime,
            $guests,
            $notes,
            $baseAmount,
            $vatAmount,
            $processingFee,
            $totalAmount
        );

        if (!$insert->execute()) {
            return ['success' => false, 'message' => 'Unable to save the reservation.'];
        }

        return ['success' => true, 'message' => 'Reservation submitted successfully.', 'res_id' => $insert->insert_id];
    }

    public function getByUserId(int $userId): array
    {
        $db = $this->connect();
        $stmt = $db->prepare(
            'SELECT r.*, c.cot_name AS cottage_name, c.cottage_type, c.cot_price AS cottage_price, c.image_path
             FROM reservations r
             INNER JOIN cottages c ON c.cot_id = r.cot_id
             WHERE r.admin_id = ?
             ORDER BY r.created_at DESC, r.res_id DESC'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllWithDetails(): array
    {
        $db = $this->connect();
        $sql = 'SELECT
                    r.*,
                    c.cot_name AS cottage_name,
                    c.cottage_type,
                    c.cot_price AS cottage_price,
                    c.image_path,
                    g.guest_fname, g.guest_lname, g.guest_email AS email, g.guest_phone AS phone_num
                FROM reservations r
                INNER JOIN cottages c ON c.cot_id = r.cot_id
                INNER JOIN guests g ON g.guest_id = r.guest_id
                ORDER BY r.created_at DESC, r.res_id DESC';
        $result = $db->query($sql);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $reservationId): ?array
    {
        $db = $this->connect();
        $stmt = $db->prepare(
            'SELECT
                r.*,
                c.cot_name AS cottage_name,
                c.cottage_type,
                c.image_path,
                g.guest_fname, g.guest_lname, g.guest_email, g.guest_phone, g.guest_address
             FROM reservations r
             INNER JOIN cottages c ON c.cot_id = r.cot_id
             INNER JOIN guests g ON g.guest_id = r.guest_id
             WHERE r.res_id = ?
             LIMIT 1'
        );
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ?: null;
    }
}
