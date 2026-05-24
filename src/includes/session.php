<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../controller/system_core.php';
require_once __DIR__ . '/../../model/Client.php';
require_once __DIR__ . '/../../model/Admin.php';
require_once __DIR__ . '/../../model/Cottage.php';
require_once __DIR__ . '/../../model/Reservation.php';
