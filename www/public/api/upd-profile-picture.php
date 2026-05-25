<?php
/**
 * API-ENDPOINT: Uppdatera profilbild
 * ==================================
 * Syftet: Låta en användare ladda upp och spara en ny profilbild
 * HTTP-metod: POST (multipart/form-data med filuppladdning)
 * Indata: uid (användar-ID), profile_picture (fil)
 * Utdata: JSON med success-status, sökväg till bild och meddelande
 * SÄKERHET: Filvalidering OK, men ingen autentisering av uid-ägarskap
 */

// ========== HEADERS: JSON-format och CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Endast POST-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

try {
    // ========== FILVALIDERING 1: Kontrollera att fil laddades upp ==========
    if (empty($_FILES['profile_picture']['name'])) {
        throw new Exception('Ingen fil laddades upp');
    }

    $file = $_FILES['profile_picture'];

    // ========== FILVALIDERING 2: Kontrollera filtyp (endast bild-format tillåts) ==========
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        throw new Exception('Endast jpg, png, gif eller webp är tillåtna');
    }

    // ========== FILVALIDERING 3: Kontrollera filstorlek (max 3 MB) ==========
    if ($file['size'] > 3 * 1024 * 1024) {
        throw new Exception('Filen är för stor (max 3 MB)');
    }

    // ========== MAPPKONFIGURATION: Skapa uploads-mapp om den inte finns ==========
    $uploadDir = __DIR__ . '/../../uploads/pfps/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true); // Skapa mappen rekursivt
    }

    // ========== INLADDNING AV DATABASKLASS ==========
    require_once '../../model/DbEgyTalk.php';
    $db = new DbEgyTalk();

    // ========== HÄMTA OCH VALIDERA UID ==========
    $uid = (int) ($_POST['uid'] ?? 0);
    if ($uid <= 0) {
        throw new Exception('Ogiltigt användar-ID');
    }

    // ========== FILBEARBETNING: Generera unikt filnamn och spara fil ==========
    // Format: pfp_[uid]_[timestamp].[extension]
    // Detta säkerställer att varje fil är unik
    $filename = 'pfp_' . $uid . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $filename;

    // Flytta den tempfilen från serverns temp-katalog till vår uploads-mapp
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // ========== DATABAS-OPERATION: Spara sökvägen i databasen ==========
        $dbPath = '/uploads/pfps/' . $filename;
        $ok = $db->updateProfilePicture($uid, $dbPath);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Profilbild uppdaterad' : 'Kunde inte spara i databasen',
            'path' => $dbPath
        ]);
    } else {
        throw new Exception('Kunde inte spara filen på servern');
    }

} catch (Exception $e) {
    // ========== ERRORHANTERING: Logga och returnera felmeddelande ==========
    error_log("Profile picture upload error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}