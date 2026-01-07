# DOKUMENTASI FITUR AUTO-TAGGING DENGAN GEMINI AI

**Dibuat:** 2026-01-07  
**Project:** My Daily Journal - Web Jurnal Pribadi  
**Fitur:** Auto-Tagging menggunakan Gemini AI

---

## 📋 DAFTAR ISI

1. [Overview Fitur](#overview-fitur)
2. [Tech Stack & Alasan Penggunaan](#tech-stack--alasan-penggunaan)
3. [Flow Kerja Auto-Tagging](#flow-kerja-auto-tagging)
4. [Struktur File & Fungsi](#struktur-file--fungsi)
5. [Penjelasan Detail Setiap File](#penjelasan-detail-setiap-file)
6. [Contoh Code & Penjelasan](#contoh-code--penjelasan)
7. [Konfigurasi & Setup](#konfigurasi--setup)
8. [Troubleshooting](#troubleshooting)

---

## 📖 OVERVIEW FITUR

### Apa itu Auto-Tagging?
Fitur Auto-Tagging adalah kemampuan sistem untuk **secara otomatis menghasilkan tag/label** yang relevan berdasarkan isi artikel jurnal menggunakan **Gemini AI** dari Google.

### Tujuan Fitur
1. **Memudahkan kategorisasi** artikel tanpa harus manual mengetik tag
2. **Konsistensi tag** - AI menghasilkan tag yang lebih konsisten
3. **Hemat waktu** pengguna saat membuat artikel
4. **Meningkatkan UX** - pengalaman pengguna lebih baik

### Batasan
- Maksimal **3 tag** per artikel (dapat dikonfigurasi)
- Tag dalam **Bahasa Indonesia**
- Panjang tag maksimal **50 karakter**
- User tetap bisa **edit/hapus** tag yang di-generate
- User bisa **tambah tag manual** setelah generate

---

## 🛠️ TECH STACK & ALASAN PENGGUNAAN

### 1. **Gemini AI (Google Generative AI)**

#### Mengapa Gemini?
- ✅ **Free tier tersedia** - cocok untuk project development
- ✅ **Bahasa Indonesia support** - bisa generate tag dalam bahasa Indonesia
- ✅ **API sederhana** - mudah diintegrasikan
- ✅ **Akurasi tinggi** - hasil tag yang relevan
- ✅ **Dokumentasi lengkap** - mudah dipelajari

#### Ciri-ciri Gemini AI:
```
Model: gemini-flash-latest (fast & efficient)
Input: Text prompt (judul + isi artikel)
Output: JSON response dengan generated text
Temperature: 0.3 (lebih konsisten, tidak terlalu kreatif)
Max Tokens: 100 (cukup untuk 3 tag)
```

#### Alternatif yang TIDAK dipilih:
- ❌ OpenAI GPT - Berbayar, tidak ada free tier
- ❌ Claude AI - Lebih mahal
- ❌ Rule-based extraction - Tidak akurat, butuh banyak rules

### 2. **PHP (Backend)**

#### Mengapa PHP?
- ✅ Project sudah menggunakan PHP
- ✅ Mudah handle HTTP request ke API
- ✅ Support cURL untuk API calls
- ✅ Easy integration dengan MySQL

#### Ciri-ciri implementasi PHP:
```php
- Prepared statements untuk security
- Error handling yang proper
- Session management untuk user data
- File upload handling untuk gambar
```

### 3. **JavaScript/AJAX (Frontend)**

#### Mengapa JavaScript + Fetch API?
- ✅ **Asynchronous** - tidak reload halaman saat generate tag
- ✅ **User experience lebih baik** - real-time feedback
- ✅ **Modern approach** - Fetch API lebih clean daripada XMLHttpRequest
- ✅ **Konsisten** dengan fitur delete & edit yang juga pakai AJAX

#### Ciri-ciri implementasi JavaScript:
```javascript
- Fetch API untuk HTTP requests
- Promise-based (.then/.catch)
- Dynamic DOM manipulation
- Bootstrap modal integration
```

### 4. **MySQL (Database)**

#### Struktur Database:
```sql
Table: article
Columns:
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- judul (VARCHAR)
- isi (TEXT)
- gambar (VARCHAR)
- tanggal (DATETIME)
- username (VARCHAR)
- tags (TEXT) -- Menyimpan tags sebagai comma-separated values
```

#### Mengapa TEXT untuk tags?
- Sederhana & fleksibel
- Tidak perlu table terpisah untuk tags
- Mudah di-parse (explode(','))

---

## 🔄 FLOW KERJA AUTO-TAGGING

### Flow Diagram (Step-by-step)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER INPUT (Frontend)                                    │
│    ├─ User mengisi judul artikel                            │
│    ├─ User mengisi isi artikel                              │
│    └─ User klik tombol "Generate Tag"                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. JAVASCRIPT VALIDATION (article.php / article_data.php)   │
│    ├─ Cek apakah judul + isi minimal 10 karakter           │
│    ├─ Jika tidak cukup → Alert error                        │
│    └─ Jika cukup → Lanjut ke AJAX request                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. AJAX REQUEST (Fetch API)                                 │
│    ├─ Method: POST                                          │
│    ├─ URL: generate_tags.php                                │
│    ├─ Body: JSON { text: "judul + isi" }                    │
│    └─ Headers: Content-Type: application/json               │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. BACKEND ENDPOINT (generate_tags.php)                     │
│    ├─ Terima POST request                                   │
│    ├─ Parse JSON body                                       │
│    ├─ Validasi input (cek text tidak kosong)                │
│    └─ Call function generateTagsWithGemini()                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. GEMINI API HANDLER (gemini_api.php)                      │
│    ├─ STEP 1: Validasi API Key                              │
│    ├─ STEP 2: Siapkan Prompt untuk Gemini                   │
│    ├─ STEP 3: Siapkan Request Body                          │
│    ├─ STEP 4: Kirim HTTP POST ke Gemini API (cURL)          │
│    ├─ STEP 5: Handle cURL errors                            │
│    ├─ STEP 6: Handle HTTP errors (quota, key salah)         │
│    ├─ STEP 7: Parse JSON response                           │
│    ├─ STEP 8: Ekstrak tags & batasi maksimal 3              │
│    └─ STEP 9: Return array tags                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. RESPONSE PROCESSING (generate_tags.php)                  │
│    ├─ Cek success/error dari gemini_api.php                │
│    ├─ Format sebagai JSON response                          │
│    └─ Return ke frontend                                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. JAVASCRIPT RESPONSE HANDLER                              │
│    ├─ Parse JSON response                                   │
│    ├─ Jika success:                                         │
│    │  ├─ Loop setiap tag                                    │
│    │  ├─ Tambahkan ke currentTags array                     │
│    │  ├─ Render tag sebagai badge                           │
│    │  └─ Update hidden input field                          │
│    └─ Jika error: Alert error message                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 8. USER INTERACTION                                         │
│    ├─ User bisa hapus tag (klik X pada badge)               │
│    ├─ User bisa tambah tag manual                           │
│    └─ Maksimal 3 tag dijaga oleh JavaScript                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 9. FORM SUBMISSION                                          │
│    ├─ User klik tombol "Simpan"                             │
│    ├─ AJAX kirim FormData ke article_data.php               │
│    ├─ Tags disimpan ke database (comma-separated)           │
│    └─ Halaman reload & tags muncul di dashboard             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 STRUKTUR FILE & FUNGSI

### File-file yang Terlibat

```
src/
├── gemini_api.php           # Core logic untuk komunikasi dengan Gemini API
├── generate_tags.php        # Endpoint API untuk generate tags
├── secrets.php              # Konfigurasi API key (RAHASIA!)
├── article.php              # Form tambah artikel + JavaScript
├── article_data.php         # Form edit artikel + JavaScript + AJAX handlers
└── index.php                # Display tags di dashboard publik
```

### Tabel Fungsi per File

| File | Fungsi Utama | Input | Output |
|------|--------------|-------|--------|
| `secrets.php` | Simpan API key secara aman | - | `GEMINI_API_KEY_SECURE` |
| `gemini_api.php` | `generateTagsWithGemini()` | Text artikel | Array tags atau error |
| `generate_tags.php` | API endpoint, routing | JSON text | JSON response |
| `article.php` | `generateTags()`, `addTag()`, `removeTag()` | Modal ID, tag | Update UI |
| `article_data.php` | `generateTagsEdit()`, tag management | Article ID | Update UI |
| `index.php` | Display tags di dashboard | - | HTML badges |

---

## 📝 PENJELASAN DETAIL SETIAP FILE

### 1. **secrets.php** - API Key Configuration

**Lokasi:** `src/secrets.php`

**Tujuan:** Menyimpan API key Gemini secara aman, terpisah dari kode utama

**Isi File:**
```php
<?php
// File ini TIDAK boleh di-commit ke Git!
// Tambahkan ke .gitignore

define('GEMINI_API_KEY_SECURE', 'YOUR_ACTUAL_API_KEY_HERE');
?>
```

**Kenapa Terpisah?**
- 🔒 **Security**: API key tidak tersimpan di kode yang di-commit
- 🔄 **Flexibility**: Bisa ganti key tanpa ubah kode
- 📦 **Best practice**: Separation of concerns

**Cara Setup:**
1. Copy dari `secrets.example.php`
2. Ganti dengan API key dari Google AI Studio
3. Jangan commit file ini ke Git!

---

### 2. **gemini_api.php** - Core Gemini Integration

**Lokasi:** `src/gemini_api.php`

**Tujuan:** Handler utama untuk komunikasi dengan Gemini API

**Fungsi Utama:**
```php
function generateTagsWithGemini($text)
```

**9 STEP dalam Fungsi:**

#### STEP 1: Validasi API Key
```php
if (empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'MASUKKAN_KEY_BARU_YANG_AMAN_DISINI') {
    return [
        'success' => false,
        'error' => 'API Key belum disetting di src/secrets.php!'
    ];
}
```
**Tujuan:** Pastikan API key sudah diset sebelum call API

#### STEP 2: Siapkan Prompt
```php
$prompt = "Analisis teks jurnal/artikel berikut dan berikan 1-3 tag yang paling relevan. 
Tag harus singkat (1-2 kata), dalam Bahasa Indonesia, dan menggambarkan topik utama.
Berikan HANYA tag saja, dipisahkan dengan koma, tanpa penjelasan tambahan.

Teks:
" . $text . "

Tag:";
```
**Kenapa Prompt ini?**
- Instruksi jelas untuk AI
- Batasi output (1-3 tag saja)
- Format output sudah ditentukan (comma-separated)
- Bahasa Indonesia

#### STEP 3: Request Body Configuration
```php
$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.3,        // Lebih konsisten
        'maxOutputTokens' => 100,    // Cukup untuk 3 tag
    ]
];
```
**Parameter Penting:**
- `temperature: 0.3` → Output lebih konsisten, tidak terlalu random
- `maxOutputTokens: 100` → Batasi panjang response

#### STEP 4: HTTP Request dengan cURL
```php
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => GEMINI_API_URL . '?key=' . GEMINI_API_KEY,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestBody),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
```
**Kenapa cURL?**
- Built-in di PHP
- Reliable untuk HTTP requests
- Support SSL verification

#### STEP 5-6: Error Handling
```php
// cURL errors
if ($curlError) {
    return ['success' => false, 'error' => 'Network error...'];
}

// HTTP errors (quota habis, key salah)
if ($httpCode !== 200) {
    return ['success' => false, 'error' => 'API Error...'];
}
```

#### STEP 7: Parse Response
```php
$responseData = json_decode($response, true);

if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    // Berbagai error handling...
}
```

#### STEP 8: Ekstrak & Batasi Tags
```php
$tagsText = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
$tags = array_map('trim', explode(',', $tagsText));
$tags = array_filter($tags, function($tag) {
    return !empty($tag) && strlen($tag) <= 50;
});
$tags = array_slice($tags, 0, 3); // 🔥 BATASAN 3 TAG
```

#### STEP 9: Return Result
```php
return [
    'success' => true,
    'tags' => array_values($tags)
];
```

---

### 3. **generate_tags.php** - API Endpoint

**Lokasi:** `src/generate_tags.php`

**Tujuan:** Routing endpoint yang menerima request dari frontend

**Flow:**
```php
// 1. Set header JSON
header('Content-Type: application/json');

// 2. Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

// 3. Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);

// 4. Validasi input
if (empty($input['text'])) {
    echo json_encode(['success' => false, 'error' => 'Text required']);
    exit;
}

// 5. Call gemini_api.php
include 'gemini_api.php';
$result = generateTagsWithGemini($input['text']);

// 6. Return response
echo json_encode($result);
```

**Kenapa File Terpisah?**
- Clean separation of concerns
- Mudah di-test
- Bisa di-reuse untuk endpoint lain

---

### 4. **article.php** - Form Tambah Artikel

**Lokasi:** `src/article.php`

**Fungsi JavaScript:**

#### `generateTags(modalId)`
```javascript
function generateTags(modalId) {
    const judul = document.getElementById('judul_' + modalId)?.value || '';
    const isi = document.getElementById('isi_' + modalId)?.value || '';
    const text = judul + ' ' + isi;
    
    // Validasi minimal 10 karakter
    if (text.trim().length < 10) {
        alert('Silakan isi judul dan isi artikel terlebih dahulu');
        return;
    }
    
    // Disable button & show loading
    btn.disabled = true;
    loading.classList.remove('d-none');
    
    // AJAX request
    fetch('generate_tags.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ text: text })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.tags) {
            // Add tags dengan proteksi max 3
            data.tags.forEach(tag => {
                tag = tag.trim().toLowerCase();
                if (currentTags[modalId].includes(tag)) return;
                
                if (currentTags[modalId].length >= 3) {
                    currentTags[modalId].shift(); // Hapus tag lama
                }
                
                currentTags[modalId].push(tag);
            });
            
            renderTags(modalId);
            updateTagsInput(modalId);
        }
    });
}
```

**Fitur Protection:**
- Batasi max 3 tag
- Auto-replace tag lama jika generate lagi
- Prevent duplicate tags

#### `addTag(modalId, tagText)` - Tambah Tag Manual
```javascript
function addTag(modalId, tagText) {
    if (currentTags[modalId].length >= 3) {
        alert('Maksimal 3 tag saja!');
        return;
    }
    currentTags[modalId].push(tagText.toLowerCase());
    renderTags(modalId);
}
```

#### `removeTag(modalId, tagText)` - Hapus Tag
```javascript
function removeTag(modalId, tagText) {
    currentTags[modalId] = currentTags[modalId].filter(t => t !== tagText);
    renderTags(modalId);
}
```

#### `renderTags(modalId)` - Render UI
```javascript
function renderTags(modalId) {
    const container = document.getElementById('tagsContainer' + capitalizeFirst(modalId));
    container.innerHTML = '';
    
    currentTags[modalId].forEach(tag => {
        const chip = document.createElement('span');
        chip.className = 'badge bg-primary d-flex align-items-center gap-1';
        chip.innerHTML = `
            ${tag}
            <button type="button" class="btn-close btn-close-white" 
                    onclick="removeTag('${modalId}', '${tag}')">
            </button>
        `;
        container.appendChild(chip);
    });
}
```

---

### 5. **article_data.php** - Edit & Display

**Sama seperti article.php tapi untuk modal Edit:**
- `generateTagsEdit(id)` - Generate untuk edit modal
- `addManualTagEdit(id)` - Tambah manual di edit
- `removeTagEdit(id, tag)` - Hapus tag di edit

**Perbedaan:**
- Menggunakan ID artikel, bukan modalId
- Edit tags muat dari database dulu (existing tags)

---

### 6. **index.php** - Display Tags di Dashboard

**Lokasi:** `src/index.php` (line 328-342)

```php
<?php
// Display tags if available
if (!empty($row["tags"])) {
    echo '<div class="mt-2">';
    $tags = explode(',', $row["tags"]);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            echo '<span class="badge bg-primary me-1 mb-1">' 
                 . htmlspecialchars($tag) . '</span>';
        }
    }
    echo '</div>';
}
?>
```

**Output:** Bootstrap badges dengan warna primary (biru)

---

## 💻 CONTOH CODE & PENJELASAN

### Contoh 1: Generate Tag di Modal Tambah

**HTML (article.php):**
```html
<div class="mb-3">
    <label class="form-label">Tags</label>
    <div class="d-flex gap-2 mb-2">
        <button type="button" class="btn btn-outline-primary btn-sm" 
                id="btnGenerateTagTambah" 
                onclick="generateTags('tambah')">
            <i class="bi bi-magic"></i> Generate Tag
        </button>
        <span id="loadingTagTambah" class="text-muted small d-none">
            <span class="spinner-border spinner-border-sm"></span> Generating...
        </span>
    </div>
    <div id="tagsContainerTambah" class="d-flex flex-wrap gap-2 mb-2">
        <!-- Tags will be displayed here as chips -->
    </div>
    <input type="hidden" name="tags" id="tagsInputTambah" value="">
</div>
```

**Penjelasan:**
- Button trigger dengan onclick
- Loading indicator (hidden by default)
- Container untuk display tags
- Hidden input untuk submit ke database

### Contoh 2: Response dari Gemini API

**Request:**
```json
POST https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=YOUR_KEY

{
  "contents": [{
    "parts": [{"text": "Analisis teks berikut..."}]
  }],
  "generationConfig": {
    "temperature": 0.3,
    "maxOutputTokens": 100
  }
}
```

**Response (Success):**
```json
{
  "candidates": [{
    "content": {
      "parts": [{
        "text": "teknologi, AI, programming"
      }]
    },
    "finishReason": "STOP"
  }]
}
```

**Response (Error - Quota):**
```json
{
  "error": {
    "code": 429,
    "message": "Quota exceeded for quota metric 'Generate Content API requests per minute'",
    "status": "RESOURCE_EXHAUSTED"
  }
}
```

### Contoh 3: Tags Di-save ke Database

**Format di Database:**
```
tags column: "teknologi, AI, programming"
```

**Parse saat Display:**
```php
$tags = explode(',', $row["tags"]); // ["teknologi", " AI", " programming"]
$tags = array_map('trim', $tags);   // ["teknologi", "AI", "programming"]
```

---

## ⚙️ KONFIGURASI & SETUP

### 1. Dapatkan Gemini API Key

```
1. Buka: https://makersuite.google.com/app/apikey
2. Login dengan Google Account
3. Click "Create API Key"
4. Copy API key yang digenerate
```

### 2. Setup secrets.php

```bash
cd src/
cp secrets.example.php secrets.php
nano secrets.php  # atau text editor lain
```

```php
<?php
define('GEMINI_API_KEY_SECURE', 'PASTE_YOUR_API_KEY_HERE');
?>
```

### 3. Tambahkan ke .gitignore

```bash
echo "src/secrets.php" >> .gitignore
```

### 4. Test API Key

Buka browser: `http://localhost:8000/generate_tags.php`

POST request dengan body:
```json
{"text": "Hari ini saya belajar programming PHP"}
```

Expected response:
```json
{
  "success": true,
  "tags": ["programming", "PHP", "belajar"]
}
```

---

## 🐛 TROUBLESHOOTING

### Error 1: "API Key belum disetting"

**Penyebab:**
- File `secrets.php` tidak ada
- API key masih default

**Solusi:**
```bash
cp secrets.example.php secrets.php
# Edit secrets.php dan masukkan API key
```

### Error 2: "Format response tidak valid dari Gemini API"

**Penyebab:**
- API key salah
- Quota habis
- Network error
- Safety filter block

**Solusi:**
```php
// Cek response detail di gemini_api.php
// Sekarang sudah ada error message yang lebih detail
```

### Error 3: "Quota exceeded"

**Penyebab:**
- Free tier Gemini punya limit per minute

**Solusi:**
- Tunggu 1 menit
- Atau upgrade ke paid tier
- Atau gunakan API key lain

### Error 4: Tags tidak muncul di dashboard

**Penyebab:**
- Tags tidak disimpan ke database
- Query SQL tidak mengambil kolom tags

**Solusi:**
```php
// Pastikan query SELECT mencakup tags
$sql = "SELECT * FROM article"; // Ini sudah include tags
```

### Error 5: Generate tag tidak bekerja

**Checklist:**
1. ✅ Browser console ada error? (F12 → Console)
2. ✅ Network tab ada request ke generate_tags.php?
3. ✅ Response dari generate_tags.php sukses?
4. ✅ API key valid?
5. ✅ Internet connection OK?

---

## 📊 MONITORING & LOGGING

### Log Request ke Gemini (Optional)

Tambahkan di `gemini_api.php` untuk debugging:

```php
// Sebelum return, log response
file_put_contents(
    'logs/gemini_requests.log',
    date('Y-m-d H:i:s') . " - Response: " . $response . "\n",
    FILE_APPEND
);
```

### Monitor Quota Usage

Check di: https://console.cloud.google.com/apis/api/generativelanguage.googleapis.com/

---

## 🎯 BEST PRACTICES

### 1. Security
- ✅ Jangan commit `secrets.php`
- ✅ Gunakan prepared statements untuk SQL
- ✅ Sanitize input dengan `htmlspecialchars()`
- ✅ Validate input sebelum kirim ke API

### 2. Performance
- ✅ Cache API responses jika memungkinkan
- ✅ Batasi panjang text yang dikirim ke API
- ✅ Timeout handling untuk slow API

### 3. User Experience
- ✅ Show loading indicator saat generate
- ✅ Error messages yang jelas
- ✅ Allow manual tag editing
- ✅ Prevent duplicate tags

### 4. Maintenance
- ✅ Log errors untuk debugging
- ✅ Monitor API quota
- ✅ Update API key secara regular
- ✅ Test setelah update dependencies

---

## 📚 REFERENSI

### Dokumentasi Official
- [Gemini API Docs](https://ai.google.dev/docs)
- [Gemini API Quickstart](https://ai.google.dev/tutorials/rest_quickstart)
- [PHP cURL Manual](https://www.php.net/manual/en/book.curl.php)
- [Fetch API MDN](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

### Tutorial & Guides
- [How to Get Gemini API Key](https://ai.google.dev/tutorials/setup)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [Bootstrap 5 Badges](https://getbootstrap.com/docs/5.3/components/badge/)

---

## 📞 SUPPORT & CONTACT

Jika ada pertanyaan atau masalah:
1. Check Troubleshooting section
2. Review code examples
3. Test dengan curl manual
4. Check Gemini API status page

---

**Dokumentasi ini dibuat untuk memudahkan pengembangan dan maintenance fitur Auto-Tagging.**

**Last Updated:** 2026-01-07  
**Version:** 1.0  
**Author:** Development Team
