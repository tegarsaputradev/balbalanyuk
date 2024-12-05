<?php

$conn = mysqli_connect("localhost", "root", "", "balbalan_yuk");

function query($query)
{
  global $conn;
  $result = mysqli_query($conn, $query);
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

function getCount($query)
{
  global $conn;
  $result = mysqli_query($conn, $query);
  if ($row = mysqli_fetch_assoc($result)) {
    return (int) $row['total'];
  }
  return 0;
}

function hapusMember($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");

  return mysqli_affected_rows($conn);
}

function hapusLpg($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM lapangan WHERE id_lapangan = $id");

  return mysqli_affected_rows($conn);
}

function hapusAdmin($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM admin WHERE id_user = $id");

  return mysqli_affected_rows($conn);
}

function hapusPesan($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM sewa WHERE id_sewa = $id");

  return mysqli_affected_rows($conn);
}

function daftar($data)
{
  global $conn;

  $username = strtolower(stripslashes($data["email"]));
  $password = $data["password"];
  $nama = $data["nama"];
  $no_handphone = $data["hp"];
  $alamat = $data["alamat"];
  $gender = $data["gender"];
  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }

  $result = mysqli_query($conn, "SELECT email FROM user WHERE email = '$username'");

  if (mysqli_fetch_assoc($result)) {
    echo "<script>
            alert('Username sudah terdaftar!');
        </script>";
    return false;
  }
  mysqli_query($conn, "INSERT INTO user (email,password,no_handphone,jenis_kelamin,nama_lengkap,alamat,foto) VALUES ('$username','$password','$no_handphone','$gender','$nama','$alamat','$upload')");
  return mysqli_affected_rows($conn);
}

function edit($data)
{
  global $conn;

  $userid = $_SESSION["id_user"];
  $username = strtolower(stripslashes($data["email"]));
  $nama = $data["nama_lengkap"];
  $no_handphone = $data["hp"];
  $gender = $data["jenis_kelamin"];
  $gambar = $data["foto"];
  $gambarLama = $data["fotoLama"];

  // Cek apakah User pilih gambar baru
  if ($_FILES["foto"]["error"] === 4) {
    $gambar = $gambarLama;
  } else {
    $gambar = upload();
  }

  $query = "UPDATE user SET email = '$username', 
  nama_lengkap = '$nama',
  no_handphone = '$no_handphone',
  jenis_kelamin = '$gender',
  foto = '$gambar'
  WHERE id_user = '$userid'
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function pesan($data)
{
    global $conn;

    $userid = $_SESSION["id_user"];
    $idlpg = $data["id_lpg"];
    $tanggal_pesan = date('Y-m-d H:i:s'); // Menyimpan tanggal dan waktu saat ini
    $lama = intval($data["jam_mulai"]); // Menganggap 'jam_mulai' adalah jumlah jam sewa
    $mulai = $data["tgl_main"];
    $mulai_waktu = strtotime($mulai); // Mengubah format datetime-local menjadi format UNIX timestamp
    $habis_waktu = $mulai_waktu + ($lama * 3600); // Menambahkan waktu sewa dalam jam ke waktu mulai
    $habis = date('Y-m-d\TH:i:s', $habis_waktu); // Mengubah format waktu kembali ke datetime-local
    $harga = $data["harga"];

    // Cek bentrokan jadwal
    $query = "SELECT * FROM sewa WHERE id_lapangan = '$idlpg' 
              AND ((jam_mulai < '$habis' AND jam_habis > '$mulai') 
              OR (jam_mulai < '$mulai' AND jam_habis > '$habis'))";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika ada bentrokan jadwal
        return false;
    } else {
        // Tidak ada bentrokan, lanjutkan dengan penyimpanan data
        $total = $lama * $harga;
        mysqli_query($conn, "INSERT INTO sewa (id_user, id_lapangan, tanggal_pesan, lama_sewa, jam_mulai, jam_habis, harga, total) 
                             VALUES ('$userid', '$idlpg', '$tanggal_pesan', '$lama', '$mulai', '$habis', '$harga', '$total')");

        return mysqli_affected_rows($conn);
    }
}


function bayar($data)
{

  global $conn;
  $id_sewa = $data["id_sewa"];
  $tanggal_upload = date('Y-m-d H:i:s'); // Menyimpan tanggal dan waktu saat ini


  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }

  mysqli_query($conn, "INSERT INTO bayar (id_sewa, bukti, tanggal_upload, konfirmasi) VALUES ('$id_sewa', '$upload', '$tanggal_upload', 'Sudah Bayar')");

  return mysqli_affected_rows($conn);
}

function tambahLpg($data)
{
  global $conn;

  $lapangan = $data["lapangan"];
  $harga = $data["harga"];
  $ket = $data["ket"];

  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }


  $query = "INSERT INTO lapangan (nama,harga,foto,keterangan) VALUES ('$lapangan','$harga','$upload','$ket')";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function upload()
{
  $namaFile = $_FILES['foto']['name'];
  $ukuranFile = $_FILES['foto']['size'];
  $error = $_FILES['foto']['error'];
  $tmpName = $_FILES['foto']['tmp_name'];

  // Cek apakah tidak ada gambar yang di upload
  if ($error === 4) {
    echo "<script>
    alert('Pilih gambar terlebih dahulu');
    </script>";
    return false;
  }

  // Cek apakah gambar
  $extensiValid = ['jpg', 'png', 'jpeg'];
  $extensiGambar = explode('.', $namaFile);
  $extensiGambar = strtolower(end($extensiGambar));

  if (!in_array($extensiGambar, $extensiValid)) {
    echo "<script>
    alert('Yang anda upload bukan gambar!');
    </script>";
    return false;
  }

  if ($ukuranFile > 1000000) {
    echo "<script>
    alert('Ukuran Gambar Terlalu Besar!');
    </script>";
    return false;
  }

  $namaFileBaru = uniqid();
  $namaFileBaru .= '.';
  $namaFileBaru .= $extensiGambar;
  // Move File
  move_uploaded_file($tmpName, '../img/' . $namaFileBaru);
  return $namaFileBaru;
}

function editLpg($data)
{
  global $conn;

  $id = $data["idlap"];
  $lapangan = $data["lapangan"];
  $ket = $data["ket"];
  $harga = $data["harga"];
  $gambarLama =  $data["fotoLama"];

  // Cek apakah User pilih gambar baru
  if ($_FILES["foto"]["error"] === 4) {
    $gambar = $gambarLama;
  } else {
    $gambar = upload();
  }


  $query = "UPDATE lapangan SET 
  nama = '$lapangan',
  keterangan = '$ket',
  harga = '$harga',
  foto = '$gambar' WHERE id_lapangan = '$id'
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}


function tambahAdmin($data)
{
  global $conn;

  $username = $data["username"];
  $password = $data["password"];
  $nama = $data["nama"];
  $no_handphone = $data["hp"];
  $email = $data["email"];

  $query = "INSERT INTO admin (username,password,nama,no_handphone,email) VALUES ('$username','$password','$nama','$no_handphone','$email')";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function editAdmin($data)
{
  global $conn;

  $id = $data["id"];
  $username = $data["username"];
  $password = $data["password"];
  $nama = $data["nama"];
  $no_handphone = $data["hp"];
  $email = $data["email"];

  $query = "UPDATE admin SET 
  username = '$username',
  password = '$password',
  nama = '$nama',
  no_handphone = '$no_handphone',
  email  = '$email' WHERE id_user = '$id'
  
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function konfirmasi($id_sewa)
{
  global $conn;

  $id = $id_sewa;

  mysqli_query($conn, "UPDATE bayar set konfirmasi = ('Terkonfirmasi') WHERE id_sewa = '$id'");
  return mysqli_affected_rows($conn);
}
