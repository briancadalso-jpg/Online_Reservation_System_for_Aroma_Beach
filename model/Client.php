<?php

require_once 'Connection.php';

class Client extends Dbh
{
    public function signUp(
        string $firstName,
        string $middleName,
        string $lastName,
        string $phoneNumber,
        string $address,
        string $email,
        string $password
    ): array {
        $db = $this->connect();

        $check = $db->prepare('SELECT admin_id FROM admins WHERE admin_email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();

        if ($check->get_result()->fetch_assoc()) {
            return ['success' => false, 'message' => 'Email address is already registered.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare(
            'INSERT INTO admins (admin_fname, admin_mname, admin_lname, admin_phone, admin_address, admin_email, admin_pass)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->bind_param(
            'sssssss',
            $firstName,
            $middleName,
            $lastName,
            $phoneNumber,
            $address,
            $email,
            $hash
        );

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Unable to create account right now.'];
        }

        return ['success' => true, 'message' => 'Admin account created successfully. You can now log in.'];
    }

    public function login(string $email, string $password): array
    {
        $db = $this->connect();
        $stmt = $db->prepare(
            'SELECT admin_id, admin_fname, admin_lname, admin_email, admin_pass
             FROM admins
             WHERE admin_email = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row || !password_verify($password, $row['admin_pass'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['admin_id'] = (int) $row['admin_id'];
        $_SESSION['admin_name'] = trim($row['admin_fname'] . ' ' . $row['admin_lname']);
        $_SESSION['admin_email'] = $row['admin_email'];
        $_SESSION['role'] = 'admin';

        return [
            'success' => true,
            'message' => 'Login successful.',
            'redirect_url' => '/aroma_resortsystem/public/admin/home.php'
        ];
    }

    public function getUserById(int $userId): ?array
    {
        $db = $this->connect();
        $stmt = $db->prepare(
            'SELECT admin_id, admin_fname, admin_mname, admin_lname, admin_phone, admin_address, admin_email, admin_created_at
             FROM admins
             WHERE admin_id = ?
             LIMIT 1'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ?: null;
    }
}
