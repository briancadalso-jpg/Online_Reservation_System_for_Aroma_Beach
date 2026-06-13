<?php

require_once 'Connection.php';

class Cottage extends Dbh
{
    public function getAll(): array
    {
        $db = $this->connect();
        $result = $db->query('SELECT * FROM cottages ORDER BY created_at ASC, cot_id DESC');

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $cottageId): ?array
    {
        $db = $this->connect();
        $stmt = $db->prepare('SELECT * FROM cottages WHERE cot_id = ? LIMIT 1');
        $stmt->bind_param('i', $cottageId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ?: null;
    }
}
