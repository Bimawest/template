<?php
include 'koneksi.php';

if (isset($_POST['konten_html'])) {
    $id_template = $_POST['id_template'];
    // Menghindari error tanda kutip di SQL
    $html = mysqli_real_escape_string($conn, $_POST['konten_html']);

    // Simpan ke tabel user_projects
    $sql = "INSERT INTO user_projects (template_id, html_editaan) VALUES ('$id_template', '$html')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Tersimpan! Perubahan Anda telah masuk ke database.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>