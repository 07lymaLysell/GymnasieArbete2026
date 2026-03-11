<?php
session_start();

// CORS-headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Bara GET-requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$result = [
    'success' => false,
    'message' => 'Failed to fetch users'
];

// Anropa getUsers-metoden
$users = $db->getUsers();

// Kontrollera om hämtning lyckades
if (!empty($users)) {
    $result['success'] = true;
    $result['message'] = 'Users fetched successfully';
    $result['users'] = $users;
} else {
    $result['message'] = 'No users found';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>