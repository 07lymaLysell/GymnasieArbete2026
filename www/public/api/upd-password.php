<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['uid']) ||
    empty($data['currentPassword']) ||
    empty($data['newPassword']) ||
    strlen($data['newPassword']) < 6
) {

    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

require '../../model/DbEgyTalk.php';
$db = new DbEgyTalk();

echo json_encode(
    $db->setPassword((int) $data['uid'], $data['currentPassword'], $data['newPassword'])
    ? ['success' => true, 'message' => 'Done']
    : ['success' => false, 'message' => 'Failed']
);