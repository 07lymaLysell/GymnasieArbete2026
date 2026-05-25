<?php
/**
 * API-ENDPOINT: Uppdatera användarens biografi
 * ============================================
 * Syftet: Låta en användare uppdatera sin profilbeskrivning (bio)
 * HTTP-metod: POST
 * Indata: uid (användar-ID), biography (biografi-text) - som JSON
 * Utdata: JSON med success-status och meddelande
 * SÄKERHETSPROBLEM: Ingen autentisering - kan ändra vilken users bio som helst!
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
    !isset($data['biography']) ||
    strlen(trim($data['biography'])) > 5000  // Bio kan inte vara längre än 5000 tecken
) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
require '../../model/DbEgyTalk.php';
$db = new DbEgyTalk();

// ========== DATABAS-OPERATION: Uppdatera biografin ==========
$ok = $db->updateBiography((int) $data['uid'], trim($data['biography']));

// ========== RESULTAT: Returnera slutresultat ==========
echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Saved' : 'Failed',
    'biography' => trim($data['biography'])
]);