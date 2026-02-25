<?php
include 'koneksi.php';

if (isset($_POST['konten_html'])) {
    $id_template = $_POST['id_template'];
    $nama_proyek = mysqli_real_escape_string($conn, $_POST['nama_proyek']);
    $html = mysqli_real_escape_string($conn, $_POST['konten_html']);

    // Simpan sebagai proyek baru
    $sql = "INSERT INTO user_projects (template_id, nama_proyek, html_editaan) 
            VALUES ('$id_template', '$nama_proyek', '$html')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Sukses! Proyek '$nama_proyek' telah disimpan.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
