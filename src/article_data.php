<?php
session_start(); // Start session at the very beginning

// Handle delete article request
if (isset($_POST['hapus'])) {
    include "koneksi.php";
    
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];

    // Delete image file if exists
    if ($gambar != '' && file_exists('img/' . $gambar)) {
        unlink('img/' . $gambar);
    }

    // Delete article from database
    $stmt = $conn->prepare("DELETE FROM article WHERE id = ?");
    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    $stmt->close();
    $conn->close();
    
    if ($hapus) {
        // Use top-level window redirect for AJAX-loaded content
        echo "<script>
            alert('Hapus data sukses');
            window.top.location.href = 'admin.php?page=article';
        </script>";
    } else {
        echo "<script>
            alert('Hapus data gagal');
            window.top.location.href = 'admin.php?page=article';
        </script>";
    }
    exit;
} // CLOSING BRACE YANG HILANG - INI PENYEBAB ERROR!

// Handle edit article request
if (isset($_POST['simpan'])) {
    include "koneksi.php";
    include "upload_foto.php";
    
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = date("Y-m-d H:i:s");
    $username = $_SESSION['username'] ?? 'admin';
    $gambar = '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $nama_gambar = $_FILES['gambar']['name'];
    $id = $_POST['id'];

    // Handle image upload
    if ($nama_gambar != '') {
        $cek_upload = upload_foto($_FILES["gambar"]);
        if ($cek_upload['status']) {
            $gambar = $cek_upload['message'];
            // Delete old image
            if (!empty($_POST['gambar_lama']) && file_exists('img/' . $_POST['gambar_lama'])) {
                unlink('img/' . $_POST['gambar_lama']);
            }
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                window.top.location.href = 'admin.php?page=article';
            </script>";
            exit;
        }
    } else {
        $gambar = $_POST['gambar_lama'];
    }

    // Update article
    $stmt = $conn->prepare("UPDATE article SET judul=?, isi=?, gambar=?, tanggal=?, username=?, tags=? WHERE id=?");
    $stmt->bind_param("ssssssi", $judul, $isi, $gambar, $tanggal, $username, $tags, $id);
    $simpan = $stmt->execute();

    $stmt->close();
    $conn->close();
    
    if ($simpan) {
        echo "<script>
            alert('Update data sukses');
            window.top.location.href = 'admin.php?page=article';
        </script>";
    } else {
        echo "<script>
            alert('Update data gagal');
            window.top.location.href = 'admin.php?page=article';
        </script>";
    }
    exit;
}
?>
 <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th class="w-25">Judul</th>
                        <th class="w-50">Isi</th>
                        <th>Tags</th>
                        <th class="w-25">Gambar</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "koneksi.php";
                    $hlm = (isset($_POST['hlm'])) ? $_POST['hlm'] : 1;
$limit = 3;
$limit_start = ($hlm - 1) * $limit;
$no = $limit_start + 1;

$sql = "SELECT * FROM article ORDER BY tanggal DESC LIMIT $limit_start, $limit";
$hasil = $conn->query($sql);

                    $no = $limit_start + 1;
                    while ($row = $hasil->fetch_assoc()) {
                        
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= $row["judul"] ?></strong>
                                <br>pada : <?= $row["tanggal"] ?>
                                <br>oleh : <?= $row["username"] ?>
                            </td>
                            <td><?= $row["isi"] ?></td>
                            <td>
                                <?php
                                if (!empty($row["tags"])) {
                                    $tags = explode(',', $row["tags"]);
                                    foreach ($tags as $tag) {
                                        $tag = trim($tag);
                                        if (!empty($tag)) {
                                            echo '<span class="badge bg-primary me-1 mb-1">' . htmlspecialchars($tag) . '</span>';
                                        }
                                    }
                                } else {
                                    echo '<span class="text-muted small">-</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($row["gambar"] != '') {
                                    if (file_exists('img/' . $row["gambar"] . '')) {
                                ?>
                                        <img src="img/<?= $row["gambar"] ?>" width="100">
                                <?php
                                    }
                                }
                                ?>
                            </td>
                            <td>
    <a href="#" title="edit" class="badge rounded-pill text-bg-success" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row["id"] ?>"><i class="bi bi-pencil"></i></a>
    <a href="#" title="delete" class="badge rounded-pill text-bg-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row["id"] ?>"><i class="bi bi-x-circle"></i></a>
</td>
<!-- Awal Modal Edit -->
<div class="modal fade" id="modalEdit<?= $row["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Article</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formGroupExampleInput" class="form-label">Judul</label>
                        <input type="hidden" name="id" value="<?= $row["id"] ?>">
                        <input type="text" class="form-control" name="judul" id="judul_edit<?= $row["id"] ?>" placeholder="Tuliskan Judul Artikel" value="<?= $row["judul"] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="floatingTextarea2">Isi</label>
                        <textarea class="form-control" placeholder="Tuliskan Isi Artikel" name="isi" id="isi_edit<?= $row["id"] ?>" required><?= $row["isi"] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Ganti Gambar</label>
                        <input type="file" class="form-control" name="gambar">
                    </div>
                    <div class="mb-3">
                        <label for="formGroupExampleInput3" class="form-label">Gambar Lama</label>
                        <?php
                        if ($row["gambar"] != '') {
                            if (file_exists('img/' . $row["gambar"] . '')) {
                        ?>
                                <br><img src="img/<?= $row["gambar"] ?>" width="100">
                        <?php
                            }
                        }
                        ?>
                        <input type="hidden" name="gambar_lama" value="<?= $row["gambar"] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnGenerateTagEdit<?= $row["id"] ?>" onclick="generateTagsEdit(<?= $row["id"] ?>)">
                                <i class="bi bi-magic"></i> Generate Tag
                            </button>
                            <span id="loadingTagEdit<?= $row["id"] ?>" class="text-muted small d-none">
                                <span class="spinner-border spinner-border-sm" role="status"></span> Generating...
                            </span>
                        </div>
                        <div id="tagsContainerEdit<?= $row["id"] ?>" class="d-flex flex-wrap gap-2 mb-2">
                            <?php
                            if (!empty($row["tags"])) {
                                $existingTags = explode(',', $row["tags"]);
                                foreach ($existingTags as $tag) {
                                    $tag = trim($tag);
                                    if (!empty($tag)) {
                                        echo '<span class="badge bg-primary d-flex align-items-center gap-1">';
                                        echo htmlspecialchars($tag);
                                        echo '<button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.6rem;" onclick="removeTagEdit(' . $row["id"] . ', \'' . addslashes($tag) . '\')"></button>';
                                        echo '</span>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" name="tags" id="tagsInputEdit<?= $row["id"] ?>" value="<?= htmlspecialchars($row["tags"] ?? '') ?>">
                        <div class="input-group mt-2">
                            <input type="text" class="form-control form-control-sm" id="manualTagEdit<?= $row["id"] ?>" placeholder="Tambah tag manual...">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="addManualTagEdit(<?= $row["id"] ?>)">Tambah</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditArticle(<?= $row['id'] ?>)">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Akhir Modal Edit -->

<!-- Awal Modal Hapus -->
<div class="modal fade" id="modalHapus<?= $row["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Hapus Article</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yakin akan menghapus artikel "<strong><?= $row["judul"] ?></strong>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteArticle(<?= $row['id'] ?>, '<?= addslashes($row['gambar']) ?>')">Hapus</button>
            </div>
        </div>
    </div>
</div>
<!-- Akhir Modal Hapus -->
                            <td>
                                <!-- untuk tombol aksi update dan delete -->
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php 
$sql1 = "SELECT * FROM article";
$hasil1 = $conn->query($sql1); 
$total_records = $hasil1->num_rows;
?>
<p>Total article : <?php echo $total_records; ?></p>
<nav class="mb-2">
    <ul class="pagination justify-content-end">
    <?php
        $jumlah_page = ceil($total_records / $limit);
        $jumlah_number = 1; //jumlah halaman ke kanan dan kiri dari halaman yang aktif
        $start_number = ($hlm > $jumlah_number)? $hlm - $jumlah_number : 1;
        $end_number = ($hlm < ($jumlah_page - $jumlah_number))? $hlm + $jumlah_number : $jumlah_page;

        if($hlm == 1){
            echo '<li class="page-item disabled"><a class="page-link" href="#">First</a></li>';
            echo '<li class="page-item disabled"><a class="page-link" href="#"><span aria-hidden="true">&laquo;</span></a></li>';
        } else {
            $link_prev = ($hlm > 1)? $hlm - 1 : 1;
            echo '<li class="page-item halaman" id="1"><a class="page-link" href="#">First</a></li>';
            echo '<li class="page-item halaman" id="'.$link_prev.'"><a class="page-link" href="#"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        for($i = $start_number; $i <= $end_number; $i++){
            $link_active = ($hlm == $i)? ' active' : '';
            echo '<li class="page-item halaman '.$link_active.'" id="'.$i.'"><a class="page-link" href="#">'.$i.'</a></li>';
        }

        if($hlm == $jumlah_page){
            echo '<li class="page-item disabled"><a class="page-link" href="#"><span aria-hidden="true">&raquo;</span></a></li>';
            echo '<li class="page-item disabled"><a class="page-link" href="#">Last</a></li>';
        } else {
        $link_next = ($hlm < $jumlah_page)? $hlm + 1 : $jumlah_page;
            echo '<li class="page-item halaman" id="'.$link_next.'"><a class="page-link" href="#"><span aria-hidden="true">&raquo;</span></a></li>';
            echo '<li class="page-item halaman" id="'.$jumlah_page.'"><a class="page-link" href="#">Last</a></li>';
        }
    ?>
    </ul>
</nav>

<script>
// Edit modal tag management
let editTags = {};

// Initialize tags from existing values when modal opens
document.addEventListener('shown.bs.modal', function(event) {
    const modal = event.target;
    if (modal.id.startsWith('modalEdit')) {
        const id = modal.id.replace('modalEdit', '');
        const tagsInput = document.getElementById('tagsInputEdit' + id);
        if (tagsInput && tagsInput.value) {
            editTags[id] = tagsInput.value.split(',').map(t => t.trim().toLowerCase()).filter(t => t);
        } else {
            editTags[id] = [];
        }
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
            if (!editTags[id]) editTags[id] = [];
            
            data.tags.forEach(tag => {
                tag = tag.trim().toLowerCase();
                
                // Skip if tag already exists
                if (editTags[id].includes(tag)) {
                    return;
                }
                
                // ⚠️ PERBAIKAN: Batasi maksimal 3 tag
                // Jika sudah ada 3 tag, hapus tag paling lama (index 0)
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
        btn.disabled = false;
        loading.classList.add('d-none');
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
        
        // ⚠️ PERBAIKAN: Batasi maksimal 3 tag
        if (editTags[id].length >= 3) {
            
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
        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalHapus' + id));
        if (modal) modal.hide();
        
        // Reload parent page
        window.top.location.href = 'admin.php?page=article';
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Function to save/update article via AJAX
function saveEditArticle(id) {
    // Get form element
    var form = document.querySelector('#modalEdit' + id + ' form');
    
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
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalEdit' + id));
        if (modal) modal.hide();
        
        // Show success message and reload
        alert('Update data sukses');
        window.top.location.href = 'admin.php?page=article';
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>