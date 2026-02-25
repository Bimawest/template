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
    <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; font-family: sans-serif; }
        main { display: flex; height: 100vh; }
        #canvas { flex: 3; height: 100vh; position: relative; overflow-y: auto; background: #eee; padding: 20px; }
        #editor-container { width: 100%; min-height: 100%; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        aside { flex: 1; min-width: 350px; background: #2c3e50; color: white; padding: 20px; z-index: 1000; overflow-y: auto; }
        .control-group { margin-bottom: 20px; border-bottom: 1px solid #34495e; padding-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border-radius: 4px; border: none; margin-bottom: 5px; box-sizing: border-box; }
        
        /* Container untuk Picker */
        #picker-container { display: flex; flex-direction: column; align-items: center; background: #fff; padding: 15px; border-radius: 8px; margin-top: 10px; }
        .color-values { color: #333; margin-top: 10px; font-family: monospace; font-size: 14px; width: 100%; text-align: center; }

        .btn-save { background: #27ae60; color: white; padding: 15px; width: 100%; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; font-size: 16px; }
        .selected-element { outline: 2px solid #f1c40f !important; }
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
            
            <div class="control-group">
                <label>Nama Proyek</label>
                <input type="text" id="nama_proyek" placeholder="Contoh: Undangan Pernikahan Ani">
            </div>

            <div class="control-group">
                <label>Ganti Gambar (Klik gambar di kiri)</label>
                <input type="file" accept="image/*" onchange="changeImage(this)">
            </div>

            <div class="control-group">
                <label>Warna (RGBA Picker)</label>
                <select id="color_target" style="margin-bottom: 10px;">
                    <option value="color">Warna Teks</option>
                    <option value="backgroundColor">Warna Latar (Background)</option>
                </select>
                <div id="picker-container">
                    <div id="colorPicker"></div>
                    <div class="color-values" id="rgba-text">rgba(255, 255, 255, 1)</div>
                </div>
            </div>

            <div class="control-group">
                <label>Font & Gaya</label>
                <select onchange="applyStyle('fontFamily', this.value)">
                    <option value="sans-serif">Default (Sans)</option>
                    <option value="'Playfair Display', serif">Playfair (Elegant)</option>
                    <option value="'Dancing Script', cursive">Dancing Script (Handwriting)</option>
                    <option value="Arial">Arial</option>
                    <option value="Georgia">Georgia</option>
                </select>
                <div style="display:flex; gap:5px; margin-top:5px;">
                    <button onclick="applyStyle('fontWeight', 'bold')" style="flex:1; padding:5px;">Bold</button>
                    <button onclick="applyStyle('fontStyle', 'italic')" style="flex:1; padding:5px;">Italic</button>
                    <button onclick="applyStyle('fontWeight', 'normal'); applyStyle('fontStyle', 'normal')" style="flex:1; padding:5px;">Reset</button>
                </div>
            </div>

            <div class="control-group">
                <label>Navigasi & Tanggal</label>
                <p style="font-size: 11px; color: #bdc3c7;">
                * Untuk Tanggal: Klik langsung pada teks tanggal di undangan.<br>
                * Untuk Maps: Klik tombol lokasi di undangan, lalu klik tombol di bawah ini.
                </p>
                <button onclick="updateMapLink()" style="width: 100%; padding: 8px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Ubah Link Google Maps
                </button>
            </div>

            <input type="hidden" id="template_id" value="<?php echo $id; ?>">
            <button class="btn-save" onclick="simpanKeDatabase()">SIMPAN PERUBAHAN</button>
            <p style="text-align:center; margin-top:15px;"><a href="template.php" style="color:#bdc3c7; text-decoration:none;">← Kembali ke Katalog</a></p>
        </aside>
    </main>

    <script>
        let selectedEl = null;

        // 1. Inisialisasi Iro.js Color Picker (RGBA)
        var colorPicker = new iro.ColorPicker("#colorPicker", {
            width: 200,
            color: "rgba(255, 255, 255, 1)",
            layout: [
                { component: iro.ui.Box },
                { component: iro.ui.Slider, options: { sliderType: 'hue' } },
                { component: iro.ui.Slider, options: { sliderType: 'alpha' } },
            ]
        });

        // Update warna ke elemen yang dipilih saat picker digeser
        colorPicker.on('color:change', function(color) {
            const rgba = color.rgbaString;
            document.getElementById('rgba-text').innerText = rgba;
            if (selectedEl) {
                const target = document.getElementById('color_target').value;
                selectedEl.style[target] = rgba;
            }
        });

        // 2. Klik elemen di kiri untuk mengedit
        document.getElementById('editor-container').addEventListener('click', function(e) {
            if (selectedEl) selectedEl.classList.remove('selected-element');
            
            selectedEl = e.target;
            if (selectedEl.id === 'editor-container') { selectedEl = null; return; }
            
            selectedEl.classList.add('selected-element');
            
            // Jika elemen adalah teks, izinkan pengetikan
            if (['H1','H2','H3','P','SPAN','B','I'].includes(selectedEl.tagName)) {
                selectedEl.setAttribute('contenteditable', 'true');
            }
        });

        // 3. Fungsi Ganti Gambar
        function changeImage(input) {
            if (selectedEl && selectedEl.tagName === 'IMG') {
                const reader = new FileReader();
                reader.onload = function(e) { selectedEl.src = e.target.result; };
                reader.readAsDataURL(input.files[0]);
            } else {
                alert("Klik pada gambar di undangan terlebih dahulu!");
            }
        }

        // 4. Fungsi Gaya Teks
        function applyStyle(prop, val) {
            if (selectedEl) selectedEl.style[prop] = val;
        }

        // 5. Simpan ke Database
        function simpanKeDatabase() {
            const htmlKonten = document.getElementById('editor-container').innerHTML;
            const templateId = document.getElementById('template_id').value;
            const namaProyek = document.getElementById('nama_proyek').value;

            if(!namaProyek) return alert("Berikan nama untuk proyek ini!");

            const formData = new FormData();
            formData.append('id_template', templateId);
            formData.append('konten_html', htmlKonten);
            formData.append('nama_proyek', namaProyek);

            fetch('simpan_aksi.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                alert(data);
                window.location.href = 'template.php';
            });
        }
    </script>
</body>
</html>
