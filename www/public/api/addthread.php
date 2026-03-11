<?php
session_start();

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

include_once('../..model/DbEgyTalk.php');
$db = new DbEgyTalk();
$result = ['success' => false, 'message' => 'Failed to create a post'];

$content = isset($_POST['content']) ? ($_POST['content']) : '';
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;

if ($uid > 0 && strlen($content) >= 1 && strlen($content) <= 10900) {   // example limits
    if ($db->addThread($uid, $content)) {
        $result['success'] = true;
        $result['message'] = 'Post created';
    } else {
        $result['message'] = 'Database error';
    }
} else {
    $result['message'] = 'Invalid content or user';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);