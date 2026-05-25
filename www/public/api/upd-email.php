<?php
/**
 * API-ENDPOINT: Uppdatera användarnamn
 * ====================================
 * Syftet: Låta en användare ändra sitt användarnamn
 * HTTP-metod: POST
 * Indata: uid (användar-ID), email (nytt användarnamn) - som JSON i request body
 * Utdata: JSON med success-status och meddelande
 * SÄKERHETSPROBLEM: Ingen autentisering - kan ändra vilken users namn som helst!
 */

// ========== HEADERS: JSON-format och CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ========== HÄMTA JSON-DATA FRÅN REQUEST BODY ==========
// I motsats till många andra endpoints använder denna JSON istället för $_POST
// json_decode() är PHP-metod för att konvertera JSON-sträng till PHP-array
$data = json_decode(file_get_contents('php://input'), true) ?? [];

// ========== VALIDERING: Kontrollera POST-metod och obligatoriska fält ==========
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['uid']) ||
    empty($data['email']) ||
    strlen(trim($data['email'])) < 9 ||      // Användarnamn måste vara minst 3 tecken
    strlen(trim($data['email'])) > 30        // Och maximalt 30 tecken
) {
    echo json_encode(['success' => false, 'message' => 'Ogiltigt användarnamn eller förfrågan']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
require '../../model/DbEgyTalk.php';
$db = new DbEgyTalk();

// ========== RENSA INDATA ==========
$email = trim($data['email']);
$uid = (int) $data['uid'];

// ========== KONTROLL: Verifiera att användarnamnet inte redan är taget (av annan) ==========
if ($db->isEmailTakenByOther($email, $uid)) {
    echo json_encode(['success' => false, 'message' => 'Användarnamnet är redan upptaget']);
    exit;
}

// ========== DATABAS-OPERATION: Uppdatera användarnamnet ==========
$ok = $db->updateEmail($uid, $email);

// ========== RESULTAT: Returnera slutresultat ==========
echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Användarnamn uppdaterat' : 'Kunde inte uppdatera användarnamn',
    'email' => $email,
]);
