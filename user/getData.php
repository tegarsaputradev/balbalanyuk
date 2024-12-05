<?php
require "../functions.php";
header('Content-Type: application/json');

// Pastikan ID lapangan disertakan dalam request
$id_lapangan = isset($_POST['id_lapangan']) ? $_POST['id_lapangan'] : '';

// Validasi ID lapangan
if (!$id_lapangan) {
    echo json_encode(['error' => 'ID lapangan tidak valid']);
    exit();
}

// Koneksi ke database dan query
$sewa = query("SELECT sewa .*, lapangan nama, user nama_lengkap
FROM sewa 
JOIN lapangan  ON sewa id_lapangan = lapangan id_lapangan
LEFT JOIN user  ON sewa id_user = user id_user
WHERE lapangan id_lapangan = '$id_lapangan'");

// Pastikan bahwa query sukses
if ($sewa) {
    echo json_encode($sewa);
} else {
    echo json_encode(['error' => 'Belum Ada Booking']);
}
?>
