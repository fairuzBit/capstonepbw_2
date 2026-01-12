<div class="container">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg"></i> Tambah Article
</button>
    <div class="row">
        <div class="table-responsive" id="article_data">
           
        </div>
        <!-- Awal Modal Tambah-->
<div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Article</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formGroupExampleInput" class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" id="judul_tambah" placeholder="Tuliskan Judul Artikel" required>
                    </div>
                    <div class="mb-3">
                        <label for="floatingTextarea2">Isi</label>
                        <textarea class="form-control" placeholder="Tuliskan Isi Artikel" name="isi" id="isi_tambah" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnGenerateTagTambah" onclick="generateTags('tambah')">
                                <i class="bi bi-magic"></i> Generate Tag
                            </button>
                            <span id="loadingTagTambah" class="text-muted small d-none">
                                <span class="spinner-border spinner-border-sm" role="status"></span> Generating...
                            </span>
                        </div>
                        <div id="tagsContainerTambah" class="d-flex flex-wrap gap-2 mb-2">
                            <!-- Tags will be displayed here as chips -->
                        </div>
                        <input type="hidden" name="tags" id="tagsInputTambah" value="">
                        <small class="text-muted">Klik Generate Tag untuk membuat tag otomatis, atau tambah manual dengan mengetik dan tekan Enter</small>
                        <div class="input-group mt-2">
                            <input type="text" class="form-control form-control-sm" id="manualTagTambah" placeholder="Tambah tag manual...">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="addManualTag('tambah')">Tambah</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Akhir Modal Tambah-->
    </div>
</div>

<script>
$(document).ready(function(){
    load_data();
    function load_data(hlm){
        $.ajax({
            url : "article_data.php",
            method : "POST",
            data : {
					            hlm: hlm
				           },
            success : function(data){
                    $('#article_data').html(data);
            }
        })
    } 
    $(document).on('click', '.halaman', function(){
    var hlm = $(this).attr("id");
    load_data(hlm);
});
});
</script>

<?php
include "upload_foto.php";

//jika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = date("Y-m-d H:i:s");
    $username = $_SESSION['username'];
    $gambar = '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $nama_gambar = $_FILES['gambar']['name'];

    //jika ada file yang dikirim  
    if ($nama_gambar != '') {
		    //panggil function upload_foto untuk cek spesifikasi file yg dikirimkan user
		    //function ini memiliki 2 keluaran yaitu status dan message
        $cek_upload = upload_foto($_FILES["gambar"]);

				//cek status true/false
        if ($cek_upload['status']) {
		        //jika true maka message berisi nama file gambar
            $gambar = $cek_upload['message'];
        } else {
		        //jika true maka message berisi pesan error, tampilkan dalam alert
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=article';
            </script>";
            die;
        }
    }

		//cek apakah ada id yang dikirimkan dari form
    if (isset($_POST['id'])) {
        //jika ada id, lakukan update data dengan id tersebut
        $id = $_POST['id'];

        if ($nama_gambar == '') {
            //jika tidak ganti gambar
            $gambar = $_POST['gambar_lama'];
        } else {
            //jika ganti gambar, hapus gambar lama
            unlink("img/" . $_POST['gambar_lama']);
        }

        $stmt = $conn->prepare("UPDATE article 
                                SET 
                                judul =?,
                                isi =?,
                                gambar = ?,
                                tanggal = ?,
                                username = ?,
                                tags = ?
                                WHERE id = ?");

        $stmt->bind_param("ssssssi", $judul, $isi, $gambar, $tanggal, $username, $tags, $id);
        $simpan = $stmt->execute();
    } else {
		    //jika tidak ada id, lakukan insert data baru
        $stmt = $conn->prepare("INSERT INTO article (judul,isi,gambar,tanggal,username,tags)
                                VALUES (?,?,?,?,?,?)");

        $stmt->bind_param("ssssss", $judul, $isi, $gambar, $tanggal, $username, $tags);
        $simpan = $stmt->execute();
    }

    if ($simpan) {
        echo "<script>
            alert('Simpan data sukses');
            document.location='admin.php?page=article';
        </script>";
    } else {
        echo "<script>
            alert('Simpan data gagal');
            document.location='admin.php?page=article';
        </script>";
    }

    $stmt->close();
    $conn->close();
}

//jika tombol hapus diklik
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];

    if ($gambar != '') {
        //hapus file gambar
        unlink("img/" . $gambar);
    }

    $stmt = $conn->prepare("DELETE FROM article WHERE id =?");

    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>
            alert('Hapus data sukses');
            document.location='admin.php?page=article';
        </script>";
    } else {
        echo "<script>
            alert('Hapus data gagal');
            document.location='admin.php?page=article';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<script>
// Tag management functions
let currentTags = {};

function generateTags(modalId) {
    const judul = document.getElementById('judul_' + modalId)?.value || '';
    const isi = document.getElementById('isi_' + modalId)?.value || '';
    const text = judul + ' ' + isi;
    
    if (text.trim().length < 10) {
        alert('Silakan isi judul dan isi artikel terlebih dahulu (minimal 10 karakter)');
        return;
    }
    
    const btn = document.getElementById('btnGenerateTag' + capitalizeFirst(modalId));
    const loading = document.getElementById('loadingTag' + capitalizeFirst(modalId));
    
    btn.disabled = true;
    loading.classList.remove('d-none');
    
    fetch('generate_tags.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ text: text })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        loading.classList.add('d-none');
        
        if (data.success && data.tags) {
            // Add generated tags - limit to max 3 tags total
            // First, check if adding new tags would exceed limit
            if (!currentTags[modalId]) currentTags[modalId] = [];
            
            data.tags.forEach(tag => {
                tag = tag.trim().toLowerCase();
                
                // Skip if tag already exists
                if (currentTags[modalId].includes(tag)) {
                    return;
                }
                
                // ⚠️ PERBAIKAN: Batasi maksimal 3 tag
                // Jika sudah ada 3 tag, hapus tag paling lama (index 0)
                if (currentTags[modalId].length >= 3) {
                    currentTags[modalId].shift(); // Hapus tag pertama (paling lama)
                }
                
                currentTags[modalId].push(tag);
            });
            
            renderTags(modalId);
            updateTagsInput(modalId);
        } else {
            alert('Error: ' + (data.error || 'Gagal generate tag'));
        }
    })
    .catch(error => {
        btn.disabled = false;
        loading.classList.add('d-none');
        alert('Error: ' + error.message);
    });
}

function addTag(modalId, tagText) {
    tagText = tagText.trim();
    if (!tagText) return;
    
    if (!currentTags[modalId]) {
        currentTags[modalId] = [];
    }
    
    // Check if tag already exists
    if (currentTags[modalId].includes(tagText.toLowerCase())) {
        return;
    }
    
    // ⚠️ PERBAIKAN: Batasi maksimal 3 tag
    // Jika sudah ada 3 tag, tampilkan peringatan
    if (currentTags[modalId].length >= 3) {
        alert('Maksimal 3 tag saja! Hapus tag yang ada terlebih dahulu untuk menambah tag baru.');
        return;
    }
    
    currentTags[modalId].push(tagText.toLowerCase());
    renderTags(modalId);
    updateTagsInput(modalId);
}

function removeTag(modalId, tagText) {
    if (!currentTags[modalId]) return;
    
    currentTags[modalId] = currentTags[modalId].filter(t => t !== tagText.toLowerCase());
    renderTags(modalId);
    updateTagsInput(modalId);
}

function renderTags(modalId) {
    const container = document.getElementById('tagsContainer' + capitalizeFirst(modalId));
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!currentTags[modalId]) return;
    
    currentTags[modalId].forEach(tag => {
        const chip = document.createElement('span');
        chip.className = 'badge bg-primary d-flex align-items-center gap-1';
        chip.innerHTML = `
            ${tag}
            <button type="button" class="btn-close btn-close-white ms-1" 
                    style="font-size: 0.6rem;" 
                    onclick="removeTag('${modalId}', '${tag}')">
            </button>
        `;
        container.appendChild(chip);
    });
}

function updateTagsInput(modalId) {
    const input = document.getElementById('tagsInput' + capitalizeFirst(modalId));
    if (!input) return;
    
    input.value = (currentTags[modalId] || []).join(', ');
}

function addManualTag(modalId) {
    const input = document.getElementById('manualTag' + capitalizeFirst(modalId));
    if (!input) return;
    
    const tag = input.value.trim();
    if (tag) {
        addTag(modalId, tag);
        input.value = '';
    }
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Handle Enter key on manual tag input
document.addEventListener('DOMContentLoaded', function() {
    const manualInputs = document.querySelectorAll('[id^="manualTag"]');
    manualInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const modalId = this.id.replace('manualTag', '').toLowerCase();
                addManualTag(modalId);
            }
        });
    });
});

// ==========================================
// EDIT MODAL LOGIC (Moved from article_data.php)
// ==========================================

// Edit modal tag management
let editTags = {};

// Initialize tags from existing values when modal opens
// We use a delegated event listener because modals might be dynamically loaded
document.addEventListener('shown.bs.modal', function(event) {
    const modal = event.target;
    if (modal.id && modal.id.startsWith('modalEdit')) {
        const id = modal.id.replace('modalEdit', '');
        const tagsInput = document.getElementById('tagsInputEdit' + id);
        
        // Reset/Initialize tags for this specific modal
        if (tagsInput && tagsInput.value) {
            // Split by comma and clean up
            editTags[id] = tagsInput.value.split(',').map(t => t.trim().toLowerCase()).filter(t => t);
        } else {
            editTags[id] = [];
        }
        
        // Render initial tags
        renderTagsEdit(id);
    }
});

function generateTagsEdit(id) {
    const judul = document.getElementById('judul_edit' + id)?.value || '';
    const isi = document.getElementById('isi_edit' + id)?.value || '';
    const text = judul + ' ' + isi;
    
    if (text.trim().length < 10) {
        alert('Silakan isi judul dan isi artikel terlebih dahulu (minimal 10 karakter)');
        return;
    }
    
    const btn = document.getElementById('btnGenerateTagEdit' + id);
    const loading = document.getElementById('loadingTagEdit' + id);
    
    if(btn) btn.disabled = true;
    if(loading) loading.classList.remove('d-none');
    
    fetch('generate_tags.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ text: text })
    })
    .then(response => response.json())
    .then(data => {
        if(btn) btn.disabled = false;
        if(loading) loading.classList.add('d-none');
        
        if (data.success && data.tags) {
            if (!editTags[id]) editTags[id] = [];
            
            data.tags.forEach(tag => {
                tag = tag.trim().toLowerCase();
                
                // Skip if tag already exists
                if (editTags[id].includes(tag)) {
                    return;
                }
                
                // Batasi maksimal 3 tag
                if (editTags[id].length >= 3) {
                    editTags[id].shift(); // Hapus tag pertama (paling lama)
                }
                
                if (tag) {
                    editTags[id].push(tag);
                }
            });
            
            renderTagsEdit(id);
            updateTagsInputEdit(id);
        } else {
            alert('Error: ' + (data.error || 'Gagal generate tag'));
        }
    })
    .catch(error => {
        if(btn) btn.disabled = false;
        if(loading) loading.classList.add('d-none');
        alert('Error: ' + error.message);
    });
}

function removeTagEdit(id, tagText) {
    if (!editTags[id]) editTags[id] = [];
    editTags[id] = editTags[id].filter(t => t !== tagText.toLowerCase());
    renderTagsEdit(id);
    updateTagsInputEdit(id);
}

function renderTagsEdit(id) {
    const container = document.getElementById('tagsContainerEdit' + id);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!editTags[id]) return;
    
    editTags[id].forEach(tag => {
        const chip = document.createElement('span');
        chip.className = 'badge bg-primary d-flex align-items-center gap-1';
        chip.innerHTML = `
            ${tag}
            <button type="button" class="btn-close btn-close-white ms-1" 
                    style="font-size: 0.6rem;" 
                    onclick="removeTagEdit(${id}, '${tag}')">
            </button>
        `;
        container.appendChild(chip);
    });
}

function updateTagsInputEdit(id) {
    const input = document.getElementById('tagsInputEdit' + id);
    if (!input) return;
    
    input.value = (editTags[id] || []).join(', ');
}

function addManualTagEdit(id) {
    const input = document.getElementById('manualTagEdit' + id);
    if (!input) return;
    
    const tag = input.value.trim().toLowerCase();
    if (tag) {
        if (!editTags[id]) editTags[id] = [];
        
        // Check if tag already exists
        if (editTags[id].includes(tag)) {
            input.value = '';
            return;
        }
        
        // Batasi maksimal 3 tag
        if (editTags[id].length >= 3) {
            alert('Maksimal 3 tag saja!');
            input.value = '';
            return;
        }
        
        editTags[id].push(tag);
        renderTagsEdit(id);
        updateTagsInputEdit(id);
        input.value = '';
    }
}

// Function to delete article via AJAX
function deleteArticle(id, gambar) {
    if(!confirm('Yakin ingin menghapus data ini?')) return;

    // Create form data
    var formData = new FormData();
    formData.append('hapus', 'hapus');
    formData.append('id', id);
    formData.append('gambar', gambar);
    
    // Send AJAX request
    fetch('article_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Close modal if open (bootstrap 5)
        const modalElement = document.getElementById('modalHapus' + id);
        if(modalElement) {
             const modal = bootstrap.Modal.getInstance(modalElement);
             if (modal) modal.hide();
        }
        
        // Reload parent page to refresh list
        // Or re-call load_data() if you want SPA feel, but reload is safer for sync
        window.location.reload();
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Function to save/update article via AJAX
function saveEditArticle(id) {
    // Get form element
    var form = document.querySelector('#modalEdit' + id + ' form');
    if(!form) return;
    
    // Create FormData from the form (handles file uploads automatically)
    var formData = new FormData(form);
    
    // Make sure we add the 'simpan' parameter
    formData.append('simpan', 'simpan');
    
    // Send AJAX request
    fetch('article_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Close modal
         const modalElement = document.getElementById('modalEdit' + id);
        if(modalElement) {
             const modal = bootstrap.Modal.getInstance(modalElement);
             if (modal) modal.hide();
        }
        
        // Show success message and reload
        alert('Update data sukses');
        window.location.reload();
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

</script>