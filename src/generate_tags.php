<?php
/**
 * API Endpoint untuk Generate Tags
 * Menerima POST request dengan parameter 'text'
 * Mengembalikan JSON dengan array tags
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hanya menerima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Include Gemini API helper
require_once 'gemini_api.php';

// Ambil data dari request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validasi input
if (!isset($data['text']) || empty(trim($data['text']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parameter text diperlukan']);
    exit();
}

$text = trim($data['text']);

// Generate tags menggunakan Gemini
$result = generateTagsWithGemini($text);

// Return response
if ($result['success']) {
    echo json_encode([
        'success' => true,
        'tags' => $result['tags']
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $result['error']
    ]);
}
?>
