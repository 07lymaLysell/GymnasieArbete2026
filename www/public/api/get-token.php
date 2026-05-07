<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

if (empty($_SESSION['CSRFToken'])) {
    $_SESSION['CSRFToken'] = bin2hex(random_bytes(32));
}

echo json_encode(['csrfToken' => $_SESSION['CSRFToken']]);