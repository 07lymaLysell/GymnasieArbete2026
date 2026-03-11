<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$result = ['success' => false, 'message' => 'Missing uid'];

if ($uid) {
    $friends = $db->getFriends($uid);
    $result['success'] = true;
    $result['friends'] = $friends;
} else {
    $result['message'] = 'Invalid uid';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>