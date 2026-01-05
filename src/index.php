<?php
include "koneksi.php";
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My daily journal</title>
    <link rel="icon" href="img/logo.png" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />

    <style>
      /* body background begin */

      body {
        background: radial-gradient(
          circle at top left,
          #0f0c29,
          #302b63,
          #24243e
        );
        background-attachment: fixed;
        color: #f0f0f0;
        font-family: "Poppins", sans-serif;
      }
      /* body background end */

      section {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
        padding: 50px 30px;
        margin: 50px auto;
        width: 90%;
      }

      .navbar-brand:hover,
      .nav-link:hover {
        color: #ff00ff !important;
        text-shadow: 0 0 10px #fd4bfd;
      }

      /* 🌃 DARK MODE */

      #article .card-img-top {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-position: center;
      }

      #gallery .carousel-item img {
        width: 100%;
        height: 700px;
        object-fit: cover;
        object-position: top;
      }

      #article .card-img-top {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-position: top;
        transition: transform 0.5s ease;
      }

      /* css jadwal */
      .flip-card {
        perspective: 1000px;
        height: 270px;
      }
      .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.7s;
        transform-style: preserve-3d;
      }
      .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
      }
      .flip-card-front,
      .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        backface-visibility: hidden;
      }
      .flip-card-back {
        transform: rotateY(180deg);
      }

      /* Warna pastel tiap hari */
      .bg-senin {
        background: linear-gradient(135deg, #7abaff, #c1e0ff);
      }
      .bg-selasa {
        background: linear-gradient(135deg, #9be7ff, #b2f7ef);
      }
      .bg-rabu {
        background: linear-gradient(135deg, #fda085, #f6d365);
      }
      .bg-kamis {
        background: linear-gradient(135deg, #a18cd1, #fbc2eb);
      }
      .bg-jumat {
        background: linear-gradient(135deg, #f6d365, #fda085);
      }
      .bg-sabtu {
        background: linear-gradient(135deg, #84fab0, #8fd3f4);
      }
      .bg-minggu {
        background: linear-gradient(135deg, #ff9a9e, #fecfef);
      }

      h2 {
        font-size: 1.8rem;
        letter-spacing: 1px;
      }

      /* DARK MODE */
      html[data-bs-theme="dark"] body {
        background: radial-gradient(
          circle at bottom right,
          #050505,
          #1a1a2e,
          #16213e
        );
        color: #e0e0e0;
      }

      body.dark-mode .navbar {
        background: rgba(20, 20, 20, 0.9) !important;
        border-bottom: 1px solid rgba(255, 0, 255, 0.3);
      }

      body.dark-mode .navbar-brand,
      body.dark-mode .nav-link {
        color: #00e0ff !important;
      }

      html[data-bs-theme="dark"] .navbar {
        background: rgba(20, 20, 20, 0.9) !important;
        border-bottom: 1px solid rgba(255, 0, 255, 0.3);
      }

      html[data-bs-theme="dark"] .navbar-brand,
      html[data-bs-theme="dark"] .nav-link {
        color: #00e0ff !important;
      }

      html[data-bs-theme="dark"] .hero-section {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #d1faff;
        border-bottom: 2px solid rgba(255, 0, 255, 0.3);
      }

      html[data-bs-theme="light"] .hero-section {
        background: #f8f9fa;
        color: #212529;
        border-bottom: 2px solid #e5e5e5;
      }

      html[data-bs-theme="dark"] section {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
      }

      /* 🌤 LIGHT MODE (putih bersih dengan bayangan lembut) */
      html[data-bs-theme="light"] body {
        background: #ffffff;
        color: #212529;
      }

      html[data-bs-theme="light"] .navbar {
        background: #ffffff !important;
        border-bottom: 1px solid #ddd;
      }

      html[data-bs-theme="light"] .navbar-brand,
      html[data-bs-theme="light"] .nav-link {
        color: #333 !important;
      }

      html[data-bs-theme="light"] section {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      }

      /* Supaya tulisan di jadwal tetap kelihatan jelas */

      html[data-bs-theme="light"] .flip-card-front h2 {
        color: #000 !important;
      }

      html[data-bs-theme="dark"] .flip-card-front h2 {
        color: #ffffff !important;
      }

      /* profil css */

      #profil img {
        transition: transform 0.6s ease;
      }

      #profil img:hover {
        transform: scale(1.03);
      }

      #profil {
        background: none !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        width: 100% !important;
        margin: 80px 0 !important;
        padding: 50px 0 !important;
      }
    </style>
  </head>
  <body>
    <!-- Nav begin -->
    <nav class="navbar navbar-expand-lg bg-body-white sticky-top">
      <div class="container">
        <a class="navbar-brand" href="#">My Daily Journal </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-dark">
            <li class="nav-item">
              <a class="nav-link" href="#">Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#article">Article</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#jadwal">Jadwal</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#gallery">Gallery</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#gallery">Profil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php" target="_blank">Login</a>
            </li>
          </ul>
          <i class="bi bi-moon-stars-fill h2 p-4" id="tombol-dark"></i>
          <i class="bi bi-brightness-high-fill h2 p-4" id="tombol-light"></i>
        </div>
      </div>
    </nav>
    <!-- nag end -->

    <!-- Hero begin -->
    <section id="hero" class="text-center p-5 text-sm-start hero-section">
      <div class="container">
        <div class="d-sm-flex flex-sm-row-reverse align-items-center">
          <img src="img/banner.png" alt="img.fuild" width="300" />
          <div>
            <h1 class="fw-bold" display-4>
              Create Memories, Save Memories, Everyday
            </h1>
            <h4 class="lead" display-6>
              Mencatat semua kegiatan sehari-hari yang ada tanpa terkecuali
            </h4>
            <h6>
              <span id="tanggal"></span>
              <span id="jam"></span>
            </h6>
          </div>
        </div>
      </div>
    </section>
    <!-- hero End -->

    <!-- article begin -->
<section id="article" class="text-center p-5">
  <div class="container">
    <h1 class="fw-bold display-4 pb-3">article</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
      <?php
      $sql = "SELECT * FROM article ORDER BY tanggal DESC";
      $hasil = $conn->query($sql); 

      while($row = $hasil->fetch_assoc()){
      ?>
        <div class="col">
          <div class="card h-100">
            <img src="img/<?= $row["gambar"]?>" class="card-img-top" alt="..." />
            <div class="card-body">
              <h5 class="card-title"><?= $row["judul"]?></h5>
              <p class="card-text">
                <?= $row["isi"]?>
              </p>
            </div>
            <div class="card-footer">
              <small class="text-body-secondary">
                <?= $row["tanggal"]?>
              </small>
            </div>
          </div>
        </div>
        <?php
      }
      ?> 
    </div>
  </div>
</section>
<!-- article end -->
    <br />
    <br />

    <!-- jadwal begin -->

    <section id="jadwal" class="text-center p-5">
      <h1 class="fw-bold display-4 pb-3">Jadwal Kuliah & Kegiatan Mahasiswa</h1>
      <div class="row row-cols-1 row-cols-md-4 g-4 justify-content-center">
        <!-- CARD TEMPLATE -->
        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-senin d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">SENIN</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2">
                  <strong>Kuliah:</strong><br />
                  - 09.30 - 12.00: Probabilitas & Statistika (H.5.11)<br />
                  - 15.30 - 18.00: Logika Informatika (H.3.9)
                </p>
                <p><strong>Kegiatan:</strong><br />- 15:30: Rapat BEM</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-selasa d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">SELASA</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2">
                  <strong>Kuliah:</strong><br />
                  - 10.20 - 12.00: Basis Data (D.2.K)<br />
                  - 12.30 - 14.10: Pemrograman Berbasis Web (D.2.J)
                </p>
                <p><strong>Kegiatan:</strong><br />- 19:00: Futsal</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-rabu d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">RABU</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2">
                  <strong>Kuliah:</strong><br />
                  - 09.30 - 12.00: Rekayasa Perangkat Lunak (H.3.10)<br />
                  - 12.30 - 15.00: Kriptografi (H.5.9)
                </p>
                <p><strong>Kegiatan:</strong><br />- 16:00: UKM Musik</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-kamis d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">KAMIS</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2">
                  <strong>Kuliah:</strong><br />
                  - 10.20 - 12.00: Basis Data (H.5.6)<br />
                  - 12.30 - 15.00: Sistem Operasi (H.3.10)
                </p>
                <p><strong>Kegiatan:</strong><br />- 16:00: UKM Musik</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-jumat d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">JUMAT</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2">
                  <strong>Kuliah:</strong><br />
                  - 09.30 - 12.00: Penambangan Data (H.4.3)
                </p>
                <p><strong>Kegiatan:</strong><br />- Pulang Kampung</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-sabtu d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">SABTU</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2"><strong>Kuliah:</strong><br />- Libur</p>
                <p>
                  <strong>Kegiatan:</strong><br />- Kerja Kelompok<br />- Nonton
                  Film
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="flip-card">
            <div class="flip-card-inner">
              <div
                class="flip-card-front bg-minggu d-flex align-items-center justify-content-center"
              >
                <h2 class="fw-bold text-white m-0">MINGGU</h2>
              </div>
              <div class="flip-card-back bg-light text-dark p-3 text-start">
                <p class="mb-2"><strong>Kuliah:</strong><br />- Libur</p>
                <p><strong>Kegiatan:</strong><br />- Cuci Baju<br />- Tidur</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- jadwal end -->

    <!-- Gallery begin -->
    <section id="gallery" class="text-center p-5 text-sm-start">
      <div class="container">
        <h1 class="fw-bold display-4 pb-2">Gallery</h1>

        <div id="carouselExample" class="carousel slide">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="img/sindoro.jpg" class="d-block w-100" alt="..." />
            </div>
            <div class="carousel-item">
              <img src="img/gunung prau.jpg" class="d-block w-100" alt="..." />
            </div>
            <div class="carousel-item">
              <img
                src="img/gunng ungaran.jpg"
                class="d-block w-100"
                alt="..."
              />
            </div>
            <div class="carousel-item">
              <img src="img/semeru.jpg" class="d-block w-100" alt="..." />
            </div>
            <div class="carousel-item">
              <img src="img/merbabu.jpg" class="d-block w-100" alt="..." />
            </div>
          </div>
          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#carouselExample"
            data-bs-slide="prev"
          >
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#carouselExample"
            data-bs-slide="next"
          >
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </section>
    <!-- Galery end -->

    <!-- Profil begin -->
    <section id="profil" class="text-center py-5">
      <div class="container">
        <h1 class="fw-bold display-4 pb-4">Profil</h1>
        <div class="row justify-content-center align-items-center">
          <div
            class="col-md-4 mb-4 mb-md-0 d-flex justify-content-center align-items-start"
            style="margin-top: -15px"
          >
            <img
              src="img/WhatsApp Image 2025-11-03 at 20.42.25 (1).png"
              alt="Foto Profil Fahmi"
              class="img-fluid rounded-circle"
              style="
                width: 230px;
                height: 230px;
                object-fit: cover;
                border: 6px solid rgba(0, 0, 0, 0.15); /* lebih tebal dan jelas */
                background-color: #fff; /* biar kontras saat light mode */
                margin-right: 40px; /* kasih jarak dari teks */
              "
            />
          </div>
          <div class="col-md-6 text-start">
            <h2 class="fw-bold mb-1">Muhammad Fahmi Zhihab</h2>
            <h5 class="text-secondary mb-3">Mahasiswa Teknik Informatika</h5>

            <table class="text-start" style="max-width: 100%">
              <tr>
                <td style="width: 180px"><strong>NIM</strong></td>
                <td>: A11.2024.15655</td>
              </tr>
              <tr>
                <td><strong>Program Studi</strong></td>
                <td>: Teknik Informatika</td>
              </tr>
              <tr>
                <td><strong>Fakultas</strong></td>
                <td>: Ilmu Komputer</td>
              </tr>
              <tr>
                <td><strong>School</strong></td>
                <td>: Universitas Dian Nuswantoro</td>
              </tr>
            </table>

            <p style="max-width: 600px justify-content">
              Saya adalah mahasiswa yang bersemangat di bidang teknologi dan
              pengembangan web. Selain kuliah, saya aktif berorganisasi dan suka
              mengeksplorasi dunia desain web, logika informatika, serta
              kreativitas digital. Hidup saya berfokus pada belajar, berkembang,
              dan memberi kebanggaan untuk keluarga 💪✨
            </p>

            <ul class="list-unstyled mt-3">
              <li>
                <i class="bi bi-envelope-fill me-2"></i>
                111202415655@mhs.dinus.ac.id
              </li>
              <li>
                <i class="bi bi-geo-alt-fill me-2"></i>
                Semarang, Indonesia
              </li>
              <li>
                <i class="bi bi-linkedin me-2"></i>
                <a
                  href="https://linkedin.com/in/fahmizhi"
                  target="_blank"
                  class="text-decoration-none"
                  >linkedin.com/in/fahmizhi</a
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <!-- Profil end -->

    <!-- Fotter Begin -->
    <footer class="text-center p-5">
      <div>
        <a
          href="https://www.instagram.com/__zheeee/"
          class="text-body text-decoration-none"
          ><i class="bi bi-instagram h2 p-2"></i
        ></a>
        <a href="#" class="text-body text-decoration-none"
          ><i class="bi bi-twitter-x h2 p-2"></i
        ></a>
        <a href="wa.me/622229081327" class="text-body text-decoration-none"
          ><i class="bi bi-whatsapp h2 p-2"></i
        ></a>
      </div>
      <div>Muhammad Fahmi Zhihab &copy 2025</div>
    </footer>
    <!-- Fotter end -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>

    <script type="text/javascript">
      window.setTimeout("tampilWaktu()", 1000);

      function tampilWaktu() {
        var waktu = new Date();
        var bulan = waktu.getMonth() + 1;

        // Baris ini akan memanggil fungsi ini lagi 1 detik kemudian
        setTimeout("tampilWaktu()", 1000);

        document.getElementById("tanggal").innerHTML =
          waktu.getDate() + "/" + bulan + "/" + waktu.getFullYear();
        document.getElementById("jam").innerHTML =
          waktu.getHours() +
          ":" +
          waktu.getMinutes() +
          ":" +
          waktu.getSeconds();
      }
    </script>
    <script type="text/javascript">
      // 1. Ambil elemen-elemen yang kita perlukan
      const htmlElement = document.documentElement; // Ini adalah tag <html>
      const darkButton = document.getElementById("tombol-dark"); // Ikon bulan
      const lightButton = document.getElementById("tombol-light"); // Ikon matahari

      // 2. Tambahkan event listener "click" ke ikon bulan
      darkButton.addEventListener("click", function () {
        htmlElement.setAttribute("data-bs-theme", "dark");
      });

      // 3. Tambahkan event listener "click" ke ikon matahari
      lightButton.addEventListener("click", function () {
        htmlElement.setAttribute("data-bs-theme", "light");
      });
    </script>
  </body>
</html>
