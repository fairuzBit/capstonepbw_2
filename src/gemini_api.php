<?php
/**
 * Gemini API Configuration & Helper
 * 
 * PENTING: Ganti YOUR_API_KEY_HERE dengan API key dari Google AI Studio
 * Dapatkan di: https://makersuite.google.com/app/apikey
 */

// Konfigurasi API
define('GEMINI_API_KEY', 'AIzaSyD0lJ1qIKU1Kl3fq1owx9Wn23QMbqrnWZQ');
// KODE BARU
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent');


/**
 * Generate tags dari teks menggunakan Gemini AI
 * 
 * @param string $text Teks jurnal/artikel
 * @return array Array of tags atau error message
 */
function generateTagsWithGemini($text) {
    // Validasi API key
    if (GEMINI_API_KEY === 'YOUR_API_KEY_HERE') {
        return [
            'success' => false,
            'error' => 'API key belum dikonfigurasi. Edit file gemini_api.php dan masukkan API key Anda.'
        ];
    }

    // Siapkan prompt untuk generate tag
    $prompt = "Analisis teks jurnal/artikel berikut dan berikan 1-3 tag yang paling relevan. 
Tag harus singkat (1-2 kata), dalam Bahasa Indonesia, dan menggambarkan topik utama.
Berikan HANYA tag saja, dipisahkan dengan koma, tanpa penjelasan tambahan.

Teks:
" . $text . "

Tag:";

    // Siapkan request body
    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 100,
        ]
    ];

    // Kirim request ke Gemini API
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => GEMINI_API_URL . '?key=' . GEMINI_API_KEY,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestBody),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Handle curl error
    if ($curlError) {
        return [
            'success' => false,
            'error' => 'Gagal menghubungi Gemini API: ' . $curlError
        ];
    }

    // Handle HTTP error
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMessage = isset($errorData['error']['message']) 
            ? $errorData['error']['message'] 
            : 'HTTP Error: ' . $httpCode;
        return [
            'success' => false,
            'error' => 'Gemini API Error: ' . $errorMessage
        ];
    }

    // Parse response
    $responseData = json_decode($response, true);
    
    if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => false,
            'error' => 'Format response tidak valid dari Gemini API'
        ];
    }

    // Ekstrak dan bersihkan tags
    $tagsText = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
    $tags = array_map('trim', explode(',', $tagsText));
    $tags = array_filter($tags, function($tag) {
        return !empty($tag) && strlen($tag) <= 50; // Filter tag kosong dan terlalu panjang
    });
    $tags = array_slice($tags, 0, 3); // Maksimal 3 tag

    return [
        'success' => true,
        'tags' => array_values($tags)
    ];
}
?>
