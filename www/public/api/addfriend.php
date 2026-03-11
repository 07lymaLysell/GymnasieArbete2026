<?php
session_start();

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$result = ['success' => false, 'message' => 'Failed to add friend'];

$uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
$fid = isset($_POST['friend_id']) ? intval($_POST['friend_id']) : 0;

if ($uid && $fid && $uid !== $fid) {
    if ($db->addFriend($uid, $fid)) {
        $result['success'] = true;
        $result['message'] = 'Friend added';
    } else {
        $result['message'] = 'Database error';
    }
} else {
    $result['message'] = 'Invalid parameters';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>