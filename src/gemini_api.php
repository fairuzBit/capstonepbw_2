<?php
/**
 * Gemini API Configuration & Helper
 * 
 * PENTING: Ganti YOUR_API_KEY_HERE dengan API key dari Google AI Studio
 * Dapatkan di: https://makersuite.google.com/app/apikey
 */
if (file_exists(__DIR__ . '/secrets.php')) {
    include __DIR__ . '/secrets.php';
} else {
    // Fallback kalau file tidak ada (misal di server lain), definisikan kosong/error
    define('GEMINI_API_KEY_SECURE', '');
}
// Konfigurasi API
define('GEMINI_API_KEY', GEMINI_API_KEY_SECURE);
// KODE BARU
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent');


/**
 * Generate tags dari teks menggunakan Gemini AI
 * 
 * Fungsi ini akan mengirim teks artikel ke Gemini AI untuk dianalisis
 * dan menghasilkan 1-3 tag yang relevan secara otomatis.
 * 
 * @param string $text Teks jurnal/artikel yang akan dianalisis
 * @return array Array berisi 'success' (bool), 'tags' (array), atau 'error' (string)
 */
function generateTagsWithGemini($text) {
    // STEP 1: Validasi API Key
    // Cek apakah API key sudah diset dengan benar di secrets.php
    // Jika kosong atau masih default, kembalikan error
    if (empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'MASUKKAN_KEY_BARU_YANG_AMAN_DISINI') {
        return [
            'success' => false,
            'error' => 'API Key belum disetting di src/secrets.php!'
        ];
    }

    // STEP 2: Siapkan Prompt untuk Gemini AI
    // Prompt ini menginstruksikan Gemini untuk:
    // - Membaca teks artikel yang diberikan
    // - Generate 1-3 tag yang relevan (JUMLAH INI DIATUR DI PROMPT)
    // - Tag harus singkat (1-2 kata) dalam Bahasa Indonesia
    // - Output hanya tag saja, dipisahkan koma
    $prompt = "Analisis teks jurnal/artikel berikut dan berikan 1-3 tag yang paling relevan. 
Tag harus singkat (1-2 kata), dalam Bahasa Indonesia, dan menggambarkan topik utama.
Berikan HANYA tag saja, dipisahkan dengan koma, tanpa penjelasan tambahan.

Teks:
" . $text . "

Tag:";

    // STEP 3: Siapkan Request Body untuk API Call
    // Format sesuai dengan API Gemini v1beta
    $requestBody = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]  // Masukkan prompt yang sudah dibuat
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,        // Nilai rendah = hasil lebih konsisten/presisi
            'maxOutputTokens' => 100,    // Batasi panjang output (cukup untuk 3 tag)
        ]
    ];

    // STEP 4: Kirim HTTP POST Request ke Gemini API
    // Menggunakan cURL untuk komunikasi dengan API
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => GEMINI_API_URL . '?key=' . GEMINI_API_KEY,
        CURLOPT_RETURNTRANSFER => true,              // Return response sebagai string
        CURLOPT_POST => true,                         // Method POST
        CURLOPT_POSTFIELDS => json_encode($requestBody), // Body dalam format JSON
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,                        // Timeout 30 detik
        CURLOPT_SSL_VERIFYPEER => true               // Verifikasi SSL certificate
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // STEP 5: Handle Error dari cURL
    // Jika ada masalah koneksi/network, kembalikan error
    if ($curlError) {
        return [
            'success' => false,
            'error' => 'Gagal menghubungi Gemini API: ' . $curlError
        ];
    }

    // STEP 6: Handle HTTP Error (status code bukan 200)
    // Jika API mengembalikan error (misal: quota habis, key salah, dll)
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

    // STEP 7: Parse Response dari Gemini API
    // Response dalam format JSON, kita decode jadi array PHP
    $responseData = json_decode($response, true);
    
    // Debug: Log response jika ada masalah (non-production only)
    if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        // Coba format alternatif atau berikan error detail
        $debugInfo = [];
        
        // Cek apakah ada error dari API
        if (isset($responseData['error'])) {
            return [
                'success' => false,
                'error' => 'Gemini API Error: ' . $responseData['error']['message']
            ];
        }
        
        // Cek apakah kandidat ada tapi kosong
        if (!isset($responseData['candidates']) || empty($responseData['candidates'])) {
            return [
                'success' => false,
                'error' => 'Gemini tidak memberikan kandidat response. Mungkin konten di-filter atau quota habis.'
            ];
        }
        
        // Cek apakah ada safety ratings yang memblock
        if (isset($responseData['candidates'][0]['finishReason']) && 
            $responseData['candidates'][0]['finishReason'] !== 'STOP') {
            $reason = $responseData['candidates'][0]['finishReason'];
            return [
                'success' => false,
                'error' => 'Response diblokir oleh Gemini (reason: ' . $reason . '). Coba dengan teks yang berbeda.'
            ];
        }
        
        // Error format tidak dikenal
        return [
            'success' => false,
            'error' => 'Format response tidak valid dari Gemini API. Response: ' . substr(json_encode($responseData), 0, 200)
        ];
    }

    // STEP 8: Ekstrak dan Bersihkan Tags (LOGIC UTAMA PEMBATASAN TAG)
    // Ambil text hasil generate dari Gemini (misal: "teknologi, AI, programming")
    $tagsText = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
    
    // Pecah string berdasarkan koma, lalu trim setiap tag
    // Contoh: "teknologi, AI, programming" => ["teknologi", "AI", "programming"]
    $tags = array_map('trim', explode(',', $tagsText));
    
    // Filter tag yang kosong atau terlalu panjang (max 50 karakter)
    // Ini untuk memastikan kualitas tag yang dihasilkan
    $tags = array_filter($tags, function($tag) {
        return !empty($tag) && strlen($tag) <= 50;
    });
    
    // ⭐ BAGIAN INI YANG MENGATUR JUMLAH TAG MAKSIMAL = 3 ⭐
    // array_slice($tags, 0, 3) => ambil maksimal 3 tag pertama saja
    // Jadi meskipun Gemini generate lebih dari 3 tag, kita hanya ambil 3 saja
    // Untuk mengubah jumlah tag, ganti angka 3 dengan angka lain (misal: 5 untuk 5 tag)
    $tags = array_slice($tags, 0 , 3);

    // STEP 9: Return hasil success dengan array tags
    return [
        'success' => true,
        'tags' => array_values($tags)  // array_values untuk reset index array
    ];
}
?>
