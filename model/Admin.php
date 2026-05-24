<?php

require_once 'Connection.php';

class Admin extends Dbh {
    public function addCottage(
        string $name,
        string $type,
        float $price,
        string $description,
        int $capacity,
        string $imagePath
    ): array {
        $db = $this->connect();
        $sql = 'INSERT INTO cottages (cot_name, cottage_type, cot_price, description, cot_capacity, image_path)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssdsis', $name, $type, $price, $description, $capacity, $imagePath);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Failed to save cottage details.'];
        }

        return ['success' => true, 'message' => 'Cottage added successfully.'];
    }

    public function updateCottage(
        int $cottageId,
        string $name,
        string $type,
        float $price,
        string $description,
        int $capacity,
        ?string $imagePath = null
    ): array {
        $db = $this->connect();

        if ($imagePath !== null) {
            $stmt = $db->prepare(
                'UPDATE cottages
                 SET cot_name = ?, cottage_type = ?, cot_price = ?, description = ?, cot_capacity = ?, image_path = ?
                 WHERE cot_id = ?'
            );
            $stmt->bind_param('ssdsisi', $name, $type, $price, $description, $capacity, $imagePath, $cottageId);
        } else {
            $stmt = $db->prepare(
                'UPDATE cottages
                 SET cot_name = ?, cottage_type = ?, cot_price = ?, description = ?, cot_capacity = ?
                 WHERE cot_id = ?'
            );
            $stmt->bind_param('ssdsii', $name, $type, $price, $description, $capacity, $cottageId);
        }

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Failed to update cottage details.'];
        }

        return ['success' => true, 'message' => 'Cottage updated successfully.'];
    }

    public function deleteCottage(int $cottageId): array
    {
        $db = $this->connect();
        $stmt = $db->prepare('DELETE FROM cottages WHERE cot_id = ?');
        $stmt->bind_param('i', $cottageId);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Unable to delete cottage.'];
        }

        return ['success' => true, 'message' => 'Cottage deleted successfully.'];
    }

    public function updateReservation(int $reservationId, string $status, float $amountPaid): array
    {
        $db = $this->connect();
        $stmt = $db->prepare(
            'UPDATE reservations
             SET status = ?, amount_paid = ?, pay_change = GREATEST(? - total_amount, 0), updated_at = NOW()
             WHERE res_id = ?'
        );
        $stmt->bind_param('sddi', $status, $amountPaid, $amountPaid, $reservationId);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Unable to update reservation.'];
        }

        return ['success' => true, 'message' => 'Reservation updated successfully.'];
    }

    public function deleteReservation(int $reservationId): array
    {
        $db = $this->connect();
        $stmt = $db->prepare('DELETE FROM reservations WHERE res_id = ?');
        $stmt->bind_param('i', $reservationId);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Unable to delete reservation.'];
        }

        return ['success' => true, 'message' => 'Reservation deleted successfully.'];
    }
}
