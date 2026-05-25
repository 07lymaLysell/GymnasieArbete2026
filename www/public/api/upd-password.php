<?php
/**
 * API-ENDPOINT: Uppdatera lösenord
 * ================================
 * Syftet: Låta en användare ändra sitt lösenord
 * HTTP-metod: POST
 * Indata: uid (användar-ID), currentPassword (nuvarande lösenord), newPassword (nytt lösenord) - som JSON
 * Utdata: JSON med success-status och meddelande
 * SÄKERHET: Verifierar nuvarande lösenord innan ändringar (bättre än andra endpoints)
 */

// ========== HEADERS: JSON-format och CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ========== HÄMTA JSON-DATA FRÅN REQUEST BODY ==========
$data = json_decode(file_get_contents('php://input'), true) ?? [];

// ========== VALIDERING: Kontrollera POST-metod och obligatoriska fält ==========
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['uid']) ||
    empty($data['currentPassword']) ||
    empty($data['newPassword']) ||
    strlen($data['newPassword']) < 6  // Nytt lösenord måste vara minst 6 tecken
) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
require '../../model/DbEgyTalk.php';
$db = new DbEgyTalk();

// ========== DATABAS-OPERATION: Uppdatera lösenord ==========
// setPassword() metoden verifierar det gamla lösenordet innan den sätter det nya
// Detta är en säker operation - det gamla lösenordet måste vara korrekt
$result = $db->setPassword((int) $data['uid'], $data['currentPassword'], $data['newPassword']);

// ========== RESULTAT: Returnera success eller error ==========
echo json_encode(
    $result
    ? ['success' => true, 'message' => 'Done']
    : ['success' => false, 'message' => 'Failed']
);