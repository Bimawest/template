<?php 
include 'koneksi.php';
$id = isset($_GET['id']) ? $_GET['id'] : die('Error: ID tidak ditemukan');
$query = mysqli_query($conn, "SELECT * FROM templates WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Pro - Bimaundangan</title>
    <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        main { display: flex; height: 100vh; }
        
        /* Area Pratinjau (Kiri) */
        #canvas { flex: 3; height: 100vh; position: relative; overflow-y: auto; background: #eee; padding: 20px; box-sizing: border-box; }
        #editor-container { 
            width: 100%; 
            min-height: 100%; 
            background: white; 
            box-shadow: 0 0 20px rgba(0,0,0,0.2); 
            transform-origin: top center;
        }
        
        /* Panel Kontrol (Kanan) */
        aside { flex: 1; min-width: 350px; background: #2c3e50; color: white; padding: 20px; z-index: 1000; overflow-y: auto; box-shadow: -5px 0 15px rgba(0,0,0,0.3); }
        .control-group { margin-bottom: 20px; border-bottom: 1px solid #34495e; padding-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #bdc3c7; }
        input, select { width: 100%; padding: 10px; border-radius: 4px; border: none; margin-bottom: 5px; box-sizing: border-box; background: #ecf0f1; }
        
        #picker-container { display: flex; flex-direction: column; align-items: center; background: #fff; padding: 15px; border-radius: 8px; margin-top: 10px; }
        .color-values { color: #333; margin-top: 10px; font-family: monospace; font-size: 14px; width: 100%; text-align: center; }

        .btn-save { background: #27ae60; color: white; padding: 15px; width: 100%; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { background: #2ecc71; }
        
        /* Indikator Elemen Terpilih */
        .selected-element { outline: 3px dashed #f1c40f !important; outline-offset: 2px; position: relative; }
        
        /* Kalender Helper */
        #hiddenDatePicker { position: absolute; opacity: 0; pointer-events: none; }
    </style>
</head>
<body>

    <input type="date" id="hiddenDatePicker">

    <main>
        <section id="canvas">
            <div id="editor-container">
                <?php echo $data['isi_html']; ?>
            </div>
        </section>

        <aside>
            <h2 style="margin-top:0;">Editor Panel</h2>
            
            <div class="control-group">
                <label>Nama Proyek</label>
                <input type="text" id="nama_proyek" placeholder="Contoh: Undangan Pernikahan Bima">
            </div>

            <div class="control-group">
                <label>Ganti Gambar</label>
                <p style="font-size:11px; color:#bdc3c7;">Klik gambar di kiri, lalu upload file baru.</p>
                <input type="file" id="imageUploader" accept="image/*" onchange="changeImage(this)">
            </div>

            <div class="control-group">
                <label>Warna (Teks/Background)</label>
                <select id="color_target">
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
                    <option value="'Dancing Script', cursive">Dancing Script</option>
                    <option value="Georgia">Georgia (Serif)</option>
                </select>
                <div style="display:flex; gap:5px; margin-top:5px;">
                    <button onclick="applyStyle('fontWeight', 'bold')" style="flex:1; padding:8px;">B</button>
                    <button onclick="applyStyle('fontStyle', 'italic')" style="flex:1; padding:8px;">I</button>
                    <button onclick="applyStyle('textAlign', 'center')" style="flex:1; padding:8px;">Center</button>
                </div>
            </div>

            <div class="control-group">
                <label>Instruksi Tanggal & Maps</label>
                <p style="font-size: 11px; color: #bdc3c7;">
                1. Klik teks <strong>Tanggal</strong> untuk buka Kalender.<br>
                2. Klik tombol <strong>Lokasi</strong> lalu klik "Ubah Link" di bawah.
                </p>
                <button onclick="updateMapLink()" style="width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Ubah Link Google Maps
                </button>
            </div>

            <input type="hidden" id="template_id" value="<?php echo $id; ?>">
            <button class="btn-save" onclick="simpanKeDatabase()">SIMPAN PERUBAHAN</button>
            <p style="text-align:center; margin-top:15px;"><a href="template.php" style="color:#bdc3c7; text-decoration:none; font-size:13px;">← Kembali ke Katalog</a></p>
        </aside>
    </main>

    <script>
    let selectedEl = null;
    const hiddenDatePicker = document.getElementById('hiddenDatePicker');
    const editorContainer = document.getElementById('editor-container');

    // --- 1. INISIALISASI COLOR PICKER (IRO.JS) ---
    var colorPicker = new iro.ColorPicker("#colorPicker", {
        width: 180,
        color: "rgba(255, 255, 255, 1)",
        layout: [
            { component: iro.ui.Box },
            { component: iro.ui.Slider, options: { sliderType: 'hue' } },
            { component: iro.ui.Slider, options: { sliderType: 'alpha' } },
        ]
    });

    colorPicker.on('color:change', function(color) {
        if (selectedEl) {
            const rgba = color.rgbaString;
            document.getElementById('rgba-text').innerText = rgba;
            const target = document.getElementById('color_target').value;
            selectedEl.style[target] = rgba;
        }
    });

    // --- 2. LOGIKA KLIK ELEMEN DI CANVAS ---
    editorContainer.addEventListener('click', function(e) {
        // Hapus highlight dari elemen sebelumnya
        if (selectedEl) selectedEl.classList.remove('selected-element');
        
        selectedEl = e.target;
        
        // Jangan pilih container utama
        if (selectedEl.id === 'editor-container') { 
            selectedEl = null; 
            return; 
        }
        
        // Tambahkan highlight kuning
        selectedEl.classList.add('selected-element');
        
        // FITUR: DETEKSI KLIK TANGGAL UNTUK POPUP KALENDER
        // Memeriksa apakah elemen memiliki class 'date' atau berisi teks bulan/tahun
        const isDateEl = selectedEl.classList.contains('date') || 
                         selectedEl.innerText.includes('202') || 
                         /Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember/i.test(selectedEl.innerText);

        if (isDateEl) {
            hiddenDatePicker.showPicker(); // Memunculkan kalender HP/Browser
        }

        // Aktifkan mode edit teks jika elemen adalah teks
        if (['H1','H2','H3','H4','H5','H6','P','SPAN','B','I','LI','A'].includes(selectedEl.tagName)) {
            selectedEl.setAttribute('contenteditable', 'true');
            selectedEl.focus();
        }
    });

    // --- 3. SINKRONISASI TANGGAL KE TEXT & COUNTDOWN ---
    hiddenDatePicker.addEventListener('change', function() {
        if (selectedEl && this.value) {
            const dateObj = new Date(this.value);
            
            // Format Indonesia untuk tampilan teks
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('id-ID', options);
            
            // Update teks pada elemen yang diklik (misal p.date)
            selectedEl.innerText = formattedDate;

            // SINKRONISASI KE COUNTDOWN (Template2.html)
            // Simpan tanggal mentah ke atribut data agar dibaca script countdown
            // Kita taruh di elemen #days atau container countdown agar tersimpan di DB
            const countdownRef = editorContainer.querySelector('.countdown-container') || editorContainer;
            countdownRef.setAttribute('data-wedding-date', this.value);

            // Update variabel JS secara live jika dimungkinkan (untuk preview)
            // Format: 'February 7, 2026 10:00:00'
            const enOptions = { month: 'long', day: 'numeric', year: 'numeric' };
            const enFormat = dateObj.toLocaleDateString('en-US', enOptions) + " 10:00:00";
            
            console.log("Tanggal disinkronkan ke Countdown: " + enFormat);
            alert("Tanggal berhasil diubah ke " + formattedDate + ". Countdown otomatis mengikuti.");
        }
    });

    // --- 4. FUNGSI GANTI GAMBAR ---
    function changeImage(input) {
        if (selectedEl && selectedEl.tagName === 'IMG') {
            const reader = new FileReader();
            reader.onload = function(e) { 
                selectedEl.src = e.target.result; 
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            alert("Klik pada Gambar di undangan terlebih dahulu!");
        }
    }

    // --- 5. FUNGSI GAYA TEKS (FONT & BOLD) ---
    function applyStyle(prop, val) {
        if (selectedEl) {
            selectedEl.style[prop] = val;
        } else {
            alert("Pilih elemen teks di undangan terlebih dahulu!");
        }
    }

    // --- 6. FUNGSI UPDATE LINK MAPS ---
    function updateMapLink() {
        const linkEl = selectedEl?.tagName === 'A' ? selectedEl : selectedEl?.closest('a');
        if (linkEl) {
            const newUrl = prompt("Masukkan Link Google Maps baru:", linkEl.href);
            if (newUrl) {
                linkEl.href = newUrl;
                alert("Link Lokasi berhasil diperbarui!");
            }
        } else {
            alert("Klik tombol 'Lokasi' atau icon peta di undangan terlebih dahulu!");
        }
    }

    // --- 7. SIMPAN KE DATABASE ---
    function simpanKeDatabase() {
        // Hilangkan seleksi kuning agar tidak ikut tersimpan ke database
        if (selectedEl) selectedEl.classList.remove('selected-element');
        
        // Nonaktifkan contenteditable agar kode HTML bersih
        const editableEls = editorContainer.querySelectorAll('[contenteditable="true"]');
        editableEls.forEach(el => el.removeAttribute('contenteditable'));

        const htmlKonten = editorContainer.innerHTML;
        const templateId = document.getElementById('template_id').value;
        const namaProyek = document.getElementById('nama_proyek').value;

        if(!namaProyek) {
            alert("Harap isi Nama Proyek!");
            return;
        }

        const formData = new FormData();
        formData.append('id_template', templateId);
        formData.append('konten_html', htmlKonten);
        formData.append('nama_proyek', namaProyek);

        // Animasi loading sederhana
        const btn = document.querySelector('.btn-save');
        btn.innerText = "MENYIMPAN...";
        btn.disabled = true;

        fetch('simpan_aksi.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.text())
        .then(data => {
            alert("Berhasil disimpan!");
            window.location.href = 'template.php';
        })
        .catch(err => {
            alert("Terjadi kesalahan saat menyimpan.");
            btn.innerText = "SIMPAN PERUBAHAN";
            btn.disabled = false;
        });
    }
</script>
</body>
</html>

