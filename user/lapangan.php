<?php
session_start();
require "../functions.php";

$loggedIn = isset($_SESSION['role']);

if ($loggedIn) {
  // Ambil ID pengguna dari session
  $id_user = $_SESSION["id_user"];

  $profil = query("SELECT * FROM user WHERE id_user = '$id_user'")[0];
} else {
  // Jika pengguna belum login, set $id_user, $lapangan, dan $profil ke null atau berikan nilai default
  $id_user = null;
  $profil = null;
}


$lapangan = query("SELECT * FROM lapangan");



if (isset($_POST["simpan"])) {
  if (edit($_POST) > 0) {
    echo "<script>
          alert('Berhasil Diubah');
          </script>";
  } else {
    echo "<script>
          alert('Gagal Diubah');
          </script>";
  }
}


if (isset($_POST["pesan"])) {
  if (pesan($_POST) > 0) {
    echo "<script>
          alert('Berhasil DiPesan');
          document.location.href = 'pesanan.php';
          </script>";
  } else {
    echo "<script>
          alert('Jadwal Sudah Di Booking');
          </script>";
  }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Basecamp Sport Center</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">


  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <style>
    .modal-backdrop {
      z-index: 1;
    }
  </style>

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="../assets/img/logo.png" alt="">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="../index.php">Beranda<br></a></li>
          <li><a href="lapangan.php" class="active">Lapangan</a></li>
          <?php if ($loggedIn) : ?>
            <li>
              <a href="pesanan.php">Pesanan</a>
            </li>
          <?php endif; ?>
          <li><a href="membership.php">Membership</a></li>
          <li><a href="promo.php">Promo</a></li>
          <li><a href="../kontak.php">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <?php if ($loggedIn) : ?>
        <!-- Jika sudah login, tampilkan tombol profil -->
        <a class="btn-getstarted" data-bs-toggle="modal" data-bs-target="#profilModal">
          <i class="bi bi-person"></i> Profil
        </a>
      <?php else : ?>
        <!-- Jika belum login, tampilkan tombol login -->
        <a href="../login.php" class="btn-getstarted" type="submit">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </a>
      <?php endif; ?>


      <!-- Modal Profil -->
      <div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="profilModalLabel">Profil Pengguna</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
              <div class="modal-body">
                <div class="row">
                  <div class="col-4 my-5">
                    <img src="../img/<?= $profil["foto"]; ?>" alt="Foto Profil" class="img-fluid ">
                  </div>
                  <div class="col-8">
                    <h5 class="mb-3"><?= $profil["nama_lengkap"]; ?></h5>
                    <p><?= $profil["jenis_kelamin"]; ?></p>
                    <p><?= $profil["email"]; ?></p>
                    <p><?= $profil["no_handphone"]; ?></p>
                    <p><?= $profil["alamat"]; ?></p>
                    <a href="../logout.php" class="btn btn-danger">Logout</a>
                    <a href="" data-bs-toggle="modal" data-bs-target="#editProfilModal" class="btn btn-inti">Edit Profil</a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Modal Profil -->

      <!-- Edit profil -->
      <div class="modal fade" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
        <div class="modal-dialog edit modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editProfilModalLabel">Edit Profil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="fotoLama" class="form-control" id="exampleInputPassword1" value="<?= $profil["foto"]; ?>">
              <div class="modal-body">
                <div class="row justify-content-center align-items-center">
                  <div class="mb-3">
                    <img src="../img/<?= $profil["foto"]; ?>" alt="Foto Profil" class="img-fluid ">
                  </div>
                  <div class="col">
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Nama Lengkap</label>
                      <input type="text" name="nama_lengkap" class="form-control" id="exampleInputPassword1" value="<?= $profil["nama_lengkap"]; ?>">
                    </div>
                    <div class="mb-3">
                      <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                      <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="Laki-laki" <?php if ($profil['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php if ($profil['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                      </select>
                    </div>
                  </div>
                  <div class="col">
                    <div class="mb-3">
                      <label for="no_handphone" class="form-label">No Telp</label>
                      <input type="number" name="no_handphone" class="form-control" id="exampleInputPassword1" value="<?= $profil["no_handphone"]; ?>">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword1" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="exampleInputPassword1" value="<?= $profil["email"]; ?>">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">alamat</label>
                    <input type="text" name="alamat" class="form-control" id="exampleInputPassword1" value="<?= $profil["alamat"]; ?>">
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Foto : </label>
                    <input type="file" name="foto" class="form-control" id="exampleInputPassword1">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-inti" name="simpan" id="simpan">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- End Edit Modal -->

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <img src="../assets/img/hero-bg.jpg" alt="">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1>Lapangan</h1>
              <p class="mb-0">Lapangan & Fasilitas</p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Page Title -->
    <section id="courses" class="courses section">
      <div class="container">

        <div class="row gy-4">

          <?php foreach ($lapangan as $row) : ?>

            <div class="col-6 col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
              <div class="course-item">
                <img src="../img/<?= $row["foto"]; ?>" class="img-fluid" alt="...">
                <div class="course-content">
                  <p class="category">Lapangan</p>
                </div>
                <div class="p-3 text-content">
                  <h3><?= $row["nama"]; ?></h3>
                  <p class="description"><?= $row["keterangan"]; ?></p>
                  <?php if ($loggedIn) : ?>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#pesanModal<?= $row["id_lapangan"]; ?>">Pesan</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jadwalModal<?= $row["id_lapangan"]; ?>" data-id="<?= $row["id_lapangan"]; ?>">
                      Jadwal
                    </button>
                  <?php else : ?>
                    <a href="../login.php" class="btn btn-success">Pesan</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Modal Pesan -->
            <div class="modal fade" id="pesanModal<?= $row["id_lapangan"]; ?>" tabindex="-1" aria-labelledby="pesanModalLabel<?= $row["id_lapangan"]; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="pesanModalLabel<?= $row["id_lapangan"]; ?>">Pesan Lapangan <?= $row["nama"]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="" method="post">
                    <div class="modal-body">
                      <!-- konten form modal -->
                      <div class="row justify-content-center align-items-center">
                        <div class="mb-3">
                          <img src="../img/<?= $row["foto"]; ?>" alt="gambar lapangan" class="img-fluid">
                        </div>
                        <div class="text-center">
                          <h6 name="harga">Harga : <?= $row["harga"]; ?></h6>
                        </div>
                        <div class="col">
                          <input type="hidden" name="id_lpg" class="form-control" id="exampleInputPassword1" value="<?= $row["id_lapangan"]; ?>">
                          <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Tanggal Main</label>
                            <input type="datetime-local" name="tgl_main" class="form-control" id="exampleInputPassword1">
                          </div>
                        </div>
                        <div class="col">
                          <input type="hidden" name="harga" class="form-control" id="exampleInputPassword1" value="<?= $row["harga"]; ?>">
                          <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Lama Main</label>
                            <input type="time" name="jam_mulai" class="form-control" id="exampleInputPassword1">
                          </div>                      
                        </div>
                        <div class="container d-flex">
                          <p class="small ms-auto">*Menit akan diabaikan</p>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary" name="pesan" id="pesan">Pesan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Modal jadwal -->
            <div class="modal fade" id="jadwalModal<?= $row["id_lapangan"]; ?>" tabindex="-1" aria-labelledby="jadwalModalLabel<?= $row["id_lapangan"]; ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="jadwalModalLabel<?= $row["id_lapangan"]; ?>">Jadwal Lapangan <?= $row["nama"]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead class="table-primary">
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Tanggal Pesan</th>
                          <th scope="col">Nama</th>
                          <th scope="col">Jam Mulai</th>
                          <th scope="col">Lama Sewa</th>
                          <th scope="col">Jam Habis</th>
                        </tr>
                      </thead>
                      <tbody id="jadwalTabelBody<?= $row["id_lapangan"]; ?>">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>



          <?php endforeach; ?>

        </div> <!-- End Course Item-->
      </div>
      <section>
  </main>

  <footer id="footer" class="footer position-relative light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-6 col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">Basecamp</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Puri Hijau</p>
            <p>Indonesia</p>
            <p class="mt-3"><strong>Phone:</strong> <span>085869161667</span></p>
            <p><strong>Email:</strong> <span>Balbalanyuk@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-whatsapp"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
          </div>
        </div>

        <div class=" col-6 col-lg-4 col-md-6 footer-links">
          <h4>Navigasi</h4>
          <div class="row">
            <div class="col-6 col-lg-4">
              <ul>
                <li><a href="#">Beranda</a></li>
                <li><a href="#">Lapangan</a></li>
                <li><a href="#">Membership</a></li>
              </ul>
            </div>
            <div class="col-6 col-lg-4">
              <ul>
                <li><a href="#">Promo</a></li>
                <li><a href="#">Kontak</a></li>
              </ul>
            </div>
          </div>
        </div>


        <div class="col-6 col-lg-4 col-md-6 footer-links">
          <h4>Syarat & Ketentuan</h4>
          <ul>
            <li><a href="#">Lihat Syarat & Ketentuan</a></li>
          </ul>
        </div>

      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <script>
    $(document).ready(function() {
      $(document).on('show.bs.modal', '[id^="jadwalModal"]', function(event) {
        // Ambil tombol yang memicu modal
        var button = $(event.relatedTarget);

        // Ambil ID lapangan dari data-id
        var idLapangan = button.data('id');

        // Debugging: Tampilkan ID lapangan di konsol
        console.log('ID Lapangan:', idLapangan);

        // Temukan modal yang relevan
        var modal = $(this);

        // Anda bisa menggunakan idLapangan untuk melakukan AJAX atau memanipulasi DOM modal
        // Lakukan AJAX untuk mendapatkan data jadwal, misalnya
        $.ajax({
          url: 'getData.php',
          type: 'POST',
          data: {
            id_lapangan: idLapangan
          },
          dataType: 'json', // Mengatur dataType menjadi jso
          success: function(response) {
            console.log('Response:', response);
            // Parsing JSON response
            var jadwal = response;
            var tbody = modal.find('tbody'); // Temukan tbody yang relevan

            // Kosongkan tabel sebelum menambah data baru
            tbody.empty();

            // Menambahkan data ke tabel
            if (response.error) {
              // Tampilkan pesan error
              tbody.append(`
                        <tr>
                            <td colspan="7">${response.error}</td>
                        </tr>
                    `);
            } else {
              // Menambahkan data ke tabel
              response.forEach(function(item, index) {
                tbody.append(`
                            <tr>
                                <th scope="row">${index + 1}</th>
                                <td>${item['tanggal_pesan']}</td>
                                <td>${item['nama_lengkap']}</td>
                                <td>${item['jam_mulai']}</td>
                                <td>${item['lama_sewa']} jam</td>
                                <td>${item['jam_habis']}</td>
                            </tr>
                        `);
              });
            }
          },
          error: function(xhr, status, error) {
            console.error('Error fetching jadwal:', error);
          }
        });
      });
    });
  </script>

</body>

</html>