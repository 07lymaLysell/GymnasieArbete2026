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
    echo json_encode(['success' => false, 'message' => 'POST only']);
    exit;
}

try {
    if (empty($_FILES['profile_picture']['name'])) {
        throw new Exception('Ingen fil laddades upp');
    }

    $file = $_FILES['profile_picture'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        throw new Exception('Endast jpg, png, gif eller webp är tillåtna');
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        throw new Exception('Filen är för stor (max 3 MB)');
    }

    // === SAME PATH STYLE AS YOUR OTHER FILES ===
    $uploadDir = __DIR__ . '/../../uploads/pfps/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    require_once '../../model/DbEgyTalk.php';
    $db = new DbEgyTalk();

    $uid = (int) ($_POST['uid'] ?? 0);
    if ($uid <= 0) {
        throw new Exception('Ogiltigt användar-ID');
    }

    $filename = 'pfp_' . $uid . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
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
    error_log("Profile picture upload error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}