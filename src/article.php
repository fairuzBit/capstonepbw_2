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
</script>