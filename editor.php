<?php 
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM templates WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Editor Pro - Bimaundangan</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; font-family: sans-serif; }
        main { display: flex; height: 100vh; }
        
        /* Sisi Kiri: Canvas */
        #canvas { 
            flex: 3; 
            height: 100vh; 
            position: relative; 
            overflow-y: hidden; /* Terkunci sebelum tombol Buka Undangan diklik */
            background: #eee;
        }

        #editor-container { width: 100%; min-height: 100%; background: white; }

        /* Sisi Kanan: Panel */
        aside { 
            flex: 1; 
            min-width: 320px; 
            background: #2c3e50; 
            color: white; 
            padding: 25px; 
            z-index: 1000; 
            box-shadow: -5px 0 15px rgba(0,0,0,0.2);
            overflow-y: auto;
        }

        .btn-save { background: #27ae60; color: white; padding: 15px; width: 100%; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; }
        [contenteditable="true"]:hover { outline: 2px dashed #f1c40f; }
    </style>
</head>
<body>
    <main>
        <section id="canvas">
            <div id="editor-container">
                <?php echo $data['isi_html']; ?>
            </div>
        </section>

        <aside>
            <h2>Editor Panel</h2>
            <p>Klik pada teks di kiri untuk mengedit.</p>
            <input type="hidden" id="template_id" value="<?php echo $id; ?>">
            <button class="btn-save" onclick="simpanKeDatabase()">SIMPAN PERUBAHAN</button>
            <hr>
            <a href="template.php" style="color: #ecf0f1; text-decoration: none;">← Kembali ke Katalog</a>
        </aside>
    </main>

    <script>
        /**
         * FUNGSI: openInvitation
         * Dipanggil saat tombol di template diklik
         */
        function openInvitation() {
            console.log("Membuka undangan...");
            const canvas = document.getElementById('canvas');
            const container = document.getElementById('editor-container');
            const hero = container.querySelector('.hero-section') || container.querySelector('#hero');

            if (hero) {
                // 1. Animasi geser ke atas
                hero.style.transition = "transform 1.2s ease-in-out";
                hero.style.transform = "translateY(-100%)";

                // 2. PERBAIKAN: Aktifkan scroll pada area canvas
                canvas.style.overflowY = "auto"; 
                
                // 3. Musik (jika ada)
                const audio = container.querySelector('audio');
                if (audio) { audio.play().catch(e => console.log("Musik tertunda")); }
            }
        }

        // Aktifkan mode edit teks secara otomatis
        document.querySelectorAll('h1, h2, h3, p, span, b').forEach(el => {
            el.setAttribute('contenteditable', 'true');
        });

        // AJAX Simpan ke Database
        function simpanKeDatabase() {
            const htmlKonten = document.getElementById('editor-container').innerHTML;
            const templateId = document.getElementById('template_id').value;

            const formData = new FormData();
            formData.append('id_template', templateId);
            formData.append('konten_html', htmlKonten);

            fetch('simpan_aksi.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => alert(data))
            .catch(err => alert("Gagal menyimpan!"));
        }
    </script>
</body>
</html>