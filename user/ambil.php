<?php
require '../functions.php'; // Pastikan koneksi database di-include

session_start();

if (!isset($_SESSION['id_user'])) {
    // Mengirimkan status 403 jika pengguna tidak terautentikasi
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Anda harus login terlebih dahulu.']);
    exit;
}

$id_user = $_SESSION['id_user'];

// Mengambil parameter halaman dari GET request
$halaman = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$jmlHalamanPerData = 5;
$awalData = ($jmlHalamanPerData * $halaman) - $jmlHalamanPerData;

// Query untuk menghitung total data
$totalDataQuery = "SELECT COUNT(*) AS total FROM sewa
    JOIN lapangan ON sewa.id_lapangan = lapangan.id_lapangan
    LEFT JOIN bayar ON sewa.id_sewa = bayar.id_sewa
    WHERE sewa.id_user = '$id_user'";
$totalData = getCount($totalDataQuery);

if ($totalData === false) {
    // Mengirimkan status 500 jika ada kesalahan dalam query
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Terjadi kesalahan pada server.']);
    exit;
}

// Menghitung jumlah halaman
$jmlHalaman = ceil($totalData / $jmlHalamanPerData);

// Query untuk mengambil data berdasarkan halaman
$dataQuery = "SELECT sewa.*, lapangan.nama, bayar.bukti, bayar.konfirmasi
    FROM sewa
    JOIN lapangan ON sewa.id_lapangan = lapangan.id_lapangan
    LEFT JOIN bayar ON sewa.id_sewa = bayar.id_sewa
    WHERE sewa.id_user = '$id_user'
    LIMIT $awalData, $jmlHalamanPerData";
$data = query($dataQuery);

if ($data === false) {
    // Mengirimkan status 500 jika ada kesalahan dalam query
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Terjadi kesalahan pada server.']);
    exit;
}

// Mengirimkan data dalam format JSON
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'totalPages' => $jmlHalaman
]);
?>
