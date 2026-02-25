<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog & Proyek - Bimaundangan</title>
    <style>
        /* Tata Letak Utama */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #f4f4f4; 
            margin: 0; 
            display: flex; 
            min-height: 100vh; 
        }

        /* Sisi Kiri: Katalog Utama */
        .katalog-section { 
            flex: 3; 
            padding: 30px; 
            border-right: 2px solid #ddd; 
        }

        /* Sisi Kanan: Editan Saya */
        .proyek-section { 
            flex: 1; 
            background: #fff; 
            padding: 25px; 
            min-width: 350px; 
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        }

        h1, h2 { color: #2c3e50; margin-bottom: 20px; }

        /* Styling Kartu Katalog (DIPERBESAR) */
        .container-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Ukuran kolom diperbesar */
            gap: 25px; 
        }
        
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 15px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-5px); }

        .card img { 
            width: 100%; 
            height: 220px; /* Tinggi gambar diperbesar */
            object-fit: cover; 
            border-radius: 8px; 
        }

        .card h3 { margin: 15px 0; font-size: 1.2rem; }

        /* Styling Daftar Proyek di Samping (DENGAN THUMBNAIL) */
        .item-proyek { 
            display: flex; 
            align-items: center; 
            padding: 15px; 
            margin-bottom: 15px; 
            background: #f9f9f9; 
            border-radius: 10px; 
            border-left: 5px solid #3498db; 
        }
        
        .item-proyek img { 
            width: 80px; /* Thumbnail proyek */
            height: 55px; 
            object-fit: cover; 
            border-radius: 6px; 
            margin-right: 15px; 
            background: #ddd;
        }

        .item-proyek-info { flex-grow: 1; }
        .item-proyek-info b { display: block; font-size: 14px; color: #34495e; margin-bottom: 8px; }

        /* Tombol */
        .btn { 
            display: inline-block; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold; 
            text-align: center;
            transition: 0.2s;
        }
        .btn-main { background: #3498db; color: white; padding: 12px; width: 90%; }
        .btn-edit { background: #3498db; color: white; padding: 6px 12px; font-size: 12px; }
        .btn-delete { background: #e74c3c; color: white; padding: 6px 12px; font-size: 12px; }
        
        .btn:hover { opacity: 0.8; }
        .action-btns { display: flex; gap: 8px; }
    </style>
</head>
<body>

    <section class="katalog-section">
        <h1>Katalog Utama</h1>
        <div class="container-grid">
            <?php
            $query = mysqli_query($conn, "SELECT * FROM templates");
            while($row = mysqli_fetch_assoc($query)) { ?>
                <div class="card">
                    <img src="img/<?php echo $row['thumbnail']; ?>" alt="Preview">
                    <h3><?php echo $row['nama_template']; ?></h3>
                    <a href="editor.php?id=<?php echo $row['id']; ?>" class="btn btn-main">Gunakan</a>
                </div>
            <?php } ?>
        </div>
    </section>

    <aside class="proyek-section">
        <h2>Editan Saya</h2>
        <div class="list-proyek">
            <?php
            // SQL JOIN: Mengambil nama_proyek dari user_projects dan thumbnail dari templates
            $sql_proyek = "SELECT up.id AS id_proyek, up.template_id, up.nama_proyek, t.thumbnail 
                           FROM user_projects up 
                           JOIN templates t ON up.template_id = t.id 
                           ORDER BY up.id DESC";
            
            $res_proyek = mysqli_query($conn, $sql_proyek);

            if (mysqli_num_rows($res_proyek) > 0) {
                while($p = mysqli_fetch_assoc($res_proyek)) { ?>
                    <div class="item-proyek">
                        <img src="img/<?php echo $p['thumbnail']; ?>" alt="thumb">
                        
                        <div class="item-proyek-info">
                            <b><?php echo $p['nama_proyek']; ?></b>
                            <div class="action-btns">
                                <a href="editor.php?id=<?php echo $p['template_id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="hapus_aksi.php?id=<?php echo $p['id_proyek']; ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('Hapus proyek ini?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php }
            } else {
                echo "<p style='color:#999; font-style:italic;'>Belum ada editan tersimpan.</p>";
            }
            ?>
        </div>
    </aside>

</body>
</html>
