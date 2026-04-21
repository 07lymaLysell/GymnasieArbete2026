<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['uid']) ||
    empty($data['username']) ||
    strlen(trim($data['username'])) < 3 ||
    strlen(trim($data['username'])) > 30
) {
    echo json_encode(['success' => false, 'message' => 'Ogiltigt användarnamn eller förfrågan']);
    exit;
}

require '../../model/DbEgyTalk.php';
$db = new DbEgyTalk();

$username = trim($data['username']);
$uid = (int) $data['uid'];

if ($db->isUsernameTakenByOther($username, $uid)) {
    echo json_encode(['success' => false, 'message' => 'Användarnamnet är redan upptaget']);
    exit;
}

$ok = $db->updateUsername($uid, $username);

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Användarnamn uppdaterat' : 'Kunde inte uppdatera användarnamn',
    'username' => $username,
]);
