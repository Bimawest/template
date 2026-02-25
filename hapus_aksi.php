<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    // Syarat Solusi: Hapus berdasarkan 'id' unik tabel proyek
    $sql = "DELETE FROM user_projects WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: template.php");
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
}
?>