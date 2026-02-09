<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Pilih Template - Bimaundangan</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; text-align: center; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .card { background: white; border-radius: 10px; width: 250px; padding: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; }
        .btn { display: block; background: #3498db; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Katalog Undangan</h1>
    <div class="container">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM templates");
        while($row = mysqli_fetch_assoc($query)) {
            echo "<div class='card'>";
            // Menunjuk ke folder img/ dan mengambil nama file dari database
            echo "<img src='img/".$row['thumbnail']."' alt='Preview'>";
            echo "<h3>".$row['nama_template']."</h3>";
            echo "<a href='editor.php?id=".$row['id']."' class='btn'>Gunakan Template</a>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>