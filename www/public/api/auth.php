<?php
session_start();

// CORS-headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Bara POST-requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$result = [
    'success' => false,
    'message' => 'Login failed'
];

// Kontrollera att username och password finns
if (
    !isset($_POST['username']) || !isset($_POST['password']) ||
    empty(trim($_POST['username'])) || empty(trim($_POST['password']))
) {
    $result['message'] = 'Username and password required';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// Hämta indata
$username = trim($_POST['username']);
$password = $_POST['password'];

// Anropa auth-metoden
$user = $db->auth($username, $password);

// Kontrollera om authentication lyckades
if (!empty($user)) {
    $result['success'] = true;
    $result['message'] = 'Login successful';
    $result['user'] = $user;
} else {
    $result['message'] = 'Invalid username or password';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);