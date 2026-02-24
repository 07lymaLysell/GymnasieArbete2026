<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'POST only']));
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['uid'], $input['biography'])) {
    exit(json_encode(['success' => false, 'message' => 'Missing uid or biography']));
}

$uid = (int) $input['uid'];
$bio = trim($input['biography']);

if (strlen($bio) > 5000) {
    exit(json_encode(['success' => false, 'message' => 'Biography too long']));
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

try {
    $updated = $db->updateBiography($uid, $bio);

    echo json_encode([
        'success' => $updated,
        'message' => $updated ? 'Saved' : 'No change (user not found?)',
        'biography' => $bio
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}