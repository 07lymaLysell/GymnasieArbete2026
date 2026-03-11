
<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$threads = $db->getAllThreads();

$result = [
    'success' => !empty($threads),
    'message' => empty($threads) ? 'No posts yet' : 'Posts loaded',
    'threads' => $threads
];

echo json_encode($result, JSON_UNESCAPED_UNICODE);