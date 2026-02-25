<?php 
include 'koneksi.php'; // Menghubungkan ke database
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyek Saya - Bimaundangan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { color: #2c3e50; }
        
        .container { display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; max-width: 1200px; margin: 0 auto; }
        
        .card { background: white; border-radius: 12px; width: 280px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: transform 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
        
        .card-img { width: 100%; height: 160px; object-fit: cover; background: #ddd; }
        
        .card-body { padding: 20px; text-align: left; }
        .card-body h3 { margin: 0 0 10px 0; font-size: 1.2rem; color: #2c3e50; }
        .card-body p { font-size: 0.9rem; color: #7f8c8d; margin-bottom: 20px; }
        
        .btn-group { display: flex; gap: 10px; }
        .btn { flex: 1; text-align: center; padding: 10px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem; }
        
        .btn-edit { background: #3498db; color: white; }
        .btn-edit:hover { background: #2980b9; }
        
        .btn-back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; font-weight: bold; }
        
        .empty-state { text-align: center; padding: 50px; background: white; border-radius: 12px; width: 100%; max-width: 600px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Daftar Proyek Undangan Anda</h1>
        <p>Lanjutkan kustomisasi undangan yang telah Anda simpan sebelumnya.</p>
    </div>

    <div class="container">
        <?php
        // Query untuk mengambil data proyek yang sudah diedit dengan menggabungkan tabel templates untuk mendapatkan nama & thumbnail
        $sql = "SELECT user_projects.id AS id_proyek, 
                       user_projects.template_id, 
                       templates.nama_template, 
                       templates.thumbnail 
                FROM user_projects 
                JOIN templates ON user_projects.template_id = templates.id 
                ORDER BY user_projects.id DESC";
        
        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_assoc($query)) {
                ?>
                <div class="card">
                    <img src="img/<?php echo $row['thumbnail']; ?>" alt="Preview" class="card-img">
                    
                    <div class="card-body">
                        <h3><?php echo $row['nama_template']; ?></h3>
                        <p>Status: Tersimpan di Database</p>
                        
                        <div class="btn-group">
                            <a href="editor.php?id=<?php echo $row['template_id']; ?>" class="btn btn-edit">Lanjutkan Edit</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            // Tampilan jika belum ada proyek yang disimpan
            echo "<div class='empty-state'>
                    <h3>Belum ada proyek.</h3>
                    <p>Silakan pilih template dari katalog terlebih dahulu.</p>
                    <a href='template.php' class='btn btn-edit' style='display:inline-block; padding: 10px 20px;'>Buka Katalog</a>
                  </div>";
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="template.php" class="btn-back">← Kembali ke Katalog Utama</a>
    </div>

</body>
</html>