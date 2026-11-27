<?php
require_once 'config/database.php';
$db = new Database();
$data = $db->sql(
    'SELECT * FROM kegiatan2 ORDER BY tanggal_kegiatan DESC, id_kegiatan DESC'
);
?>



 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan & Agenda - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/kegiatan.css">
</head>
<body class="halaman-kegiatan">

    <!-- HEADER / NAVBAR -->
    <header class="navbar">
        <div class="logo-container">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo Katasapa" class="logo-img" onerror="this.style.display='none'">
            <div>
                <h1 class="logo-text">KATASAPA</h1>
                <p class="logo-subtext">Komunitas Teater dan Sastra Perwira</p>
            </div>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php">Tentang Kami</a></li>
                <li><a href="kegiatan.php" class="active">Kegiatan</a></li>
                <li><a href="karya.php">Karya</a></li>
                <li><a href="dokumentasi.php">Dokumentasi</a></li>
            </ul>
        </nav>
        <a href="admin/login_user.php" class="btn-login">Admin</a>
    </header>

    <!-- BANNER HEADER HALAMAN -->
    <section class="page-header">
        <h1>Agenda & Kegiatan</h1>
        <p>Jadwal workshop, diskusi sastra, latihan rutin, dan pementasan teater oleh KATASAPA Purbalingga.</p>
    </section>
    <br>

    <!-- KONTEN UTAMA -->
    <main class="content-container">
        
        <!-- Filter Kategori -->
        <div class="filter-kegiatan">
            <button class="filter-btn active" onclick="filterKegiatan('Semua', this)">Semua</button>
            <button class="filter-btn" onclick="filterKegiatan('Workshop', this)">Workshop</button>
            <button class="filter-btn" onclick="filterKegiatan('Diskusi', this)">Diskusi</button>
            <button class="filter-btn" onclick="filterKegiatan('Pertunjukan', this)">Pertunjukan</button>
        </div>

        <!-- Grid Kartu Kegiatan -->
        <div class="kegiatan-grid" id="kegiatanGrid">
            
            <!-- Kegiatan 1 (ID: 1) -->
            
<?php
if (!$data):
?>
    <p class="content-empty">Belum ada data kegiatan yang tersedia.</p>
<?php
endif;
foreach ($data as $d) {

    // Mengubah format tanggal
    $tanggal = date('d', strtotime($d['tanggal_kegiatan']));

    $bulan = [
        1 => 'JAN', 'FEB', 'MAR', 'APR',
        'MEI', 'JUN', 'JUL', 'AGU',
        'SEP', 'OKT', 'NOV', 'DES'
    ];

    $nomor_bulan = (int) date('n', strtotime($d['tanggal_kegiatan']));
    $nama_bulan = $bulan[$nomor_bulan];

    $poster = !empty($d['poster'])
        ? $d['poster']
        : 'assets/img/instagram/ruang-lain-workshop.webp';
?>

    <a href="detail_kegiatan.php?id=<?= $d['id_kegiatan'] ?>"
       class="card-kegiatan-link"
       data-kategori="<?= htmlspecialchars($d['kategori_kegiatan']) ?>">

        <article class="card-kegiatan">

            <img class="card-kegiatan-img"
                 src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?= htmlspecialchars($d['judul_kegiatan'], ENT_QUOTES, 'UTF-8'); ?>"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-workshop.webp';">

            <div class="card-kegiatan-body">

                <div class="tanggal-box">
                    <span class="tgl"><?= $tanggal ?></span>
                    <span class="bln"><?= $nama_bulan ?></span>
                </div>

                <div class="info-kegiatan">

                    <h3>
                        <?= htmlspecialchars($d['judul_kegiatan']) ?>
                    </h3>

                    <p class="lokasi">
                        📍 <?= htmlspecialchars($d['lokasi_kegiatan']) ?>
                    </p>

                    <p class="waktu">
                        🕒 <?=htmlspecialchars($d['waktu_kegiatan'])?>
                        WIB
                    </p>

                    <p class="deskripsi">
                        <?= htmlspecialchars($d['deskripsi_singkat']) ?>
                    </p>

                    <span class="badge-kategori">
                        <?= htmlspecialchars($d['kategori_kegiatan']) ?>
                    </span>

                </div>
            </div>
        </article>
    </a>

<?php
}
?>

        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <p><strong>KATASAPA</strong> © 2026 KATASAPA. Semua Hak Dilindungi.</p>
        <p>Instagram: <a href="https://www.instagram.com/katasapa.pbg/" target="_blank" rel="noopener noreferrer">@katasapa.pbg</a></p>
    </footer>

    <!-- Script Filter Sederhana -->
    <script>
        function filterKegiatan(kategori_semua, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filter berdasarkan bungkus link kartu
            const links = document.querySelectorAll('.card-kegiatan-link');
            links.forEach(link => {
                if (kategori_semua === 'Semua' || link.getAttribute('data-kategori') === kategori_semua) {
                    link.style.display = 'block';
                } else {
                    link.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>