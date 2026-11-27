<?php
require_once 'config/database.php';
$db = new Database();
$data = $db->sql(
    'SELECT * FROM karya ORDER BY tanggal_publikasi DESC, id_karya DESC'
);
?>

 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/desain_karya.css">
</head>
<body class="halaman-karya">

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
                <li><a href="kegiatan.php">Kegiatan</a></li>
                <li><a href="karya.php">Karya</a></li>
                <li><a href="dokumentasi.php">Dokumentasi</a></li>
            </ul>
        </nav>
        <a href="admin/login_user.php" class="btn-login">Admin</a>
    </header>

    <!-- BANNER HEADER HALAMAN -->
    <section class="page-header">
        <h1>Karya</h1>
        <p>Karya oleh KATASAPA Purbalingga.</p>
    </section>
    <br>

    <!-- KONTEN UTAMA -->
    <main class="content-container">
        
        <!-- Filter Kategori -->
        <div class="filter-karya">
            <button class="filter-btn active" onclick="filterKarya('Semua', this)">Semua</button>
            <button class="filter-btn" onclick="filterKarya('Puisi', this)">Puisi</button>
            <button class="filter-btn" onclick="filterKarya('Esai', this)">Esai</button>
            <button class="filter-btn" onclick="filterKarya('Cerpen', this)">Cerpen</button>
        </div>

        <!-- Grid Kartu Karya -->
        <div class="karya-grid" id="karyaGrid">
            
            <!-- Karya 1 (ID: 1) -->
            
<?php
if (!$data):
?>
    <p class="content-empty">Belum ada karya yang dipublikasikan.</p>
<?php
endif;
foreach ($data as $d) {

    // Mengubah format tanggal
    $tanggal = date('d', strtotime($d['tanggal_publikasi']));

    $bulan = [
        1 => 'JAN', 'FEB', 'MAR', 'APR',
        'MEI', 'JUN', 'JUL', 'AGU',
        'SEP', 'OKT', 'NOV', 'DES'
    ];

    $nomor_bulan = (int) date('n', strtotime($d['tanggal_publikasi']));
    $nama_bulan = $bulan[$nomor_bulan];

    // Format waktu
    //$waktu_mulai = !empty($d['waktu_kegiatan'])
       //? date('H.i', strtotime($d['waktu_kegiatan']))
      //  : '';

    //$waktu_selesai = !empty($d['waktu_selesai'])
        //? date('H.i', strtotime($d['waktu_selesai']))
        //: 'Selesai';
?>

    <a href="detail_karya.php?id=<?= $d['id_karya'] ?>"
       class="card-karya-link"
       data-kategori="<?= htmlspecialchars($d['kategori_karya']) ?>">

        <article class="card-karya">

            <?php
            $poster = !empty($d['poster_karya'])
                ? $d['poster_karya']
                : 'assets/img/instagram/ruang-lain-poster.webp';
            ?>
            <img class="card-karya-img"
                 src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?= htmlspecialchars($d['judul_karya'], ENT_QUOTES, 'UTF-8'); ?>"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-poster.webp';">

            <div class="card-karya-body">

                <div class="tanggal-box">
                    <span class="tgl"><?= $tanggal ?></span>
                    <span class="bln"><?= $nama_bulan ?></span>
                </div>

                <div class="info-karya">

                    <h3>
                        <?= htmlspecialchars($d['judul_karya']) ?>
                    </h3>

                    <p class="penulis">
                        📍 <?= htmlspecialchars($d['penulis']) ?>
                    </p>

                    <p class="waktu-publikasi">
                        🕒 <?=htmlspecialchars($d['tanggal_publikasi'])?>
                        WIB
                    </p>

                    <p class="deskripsi">
                        <?= htmlspecialchars($d['deskripsi_singkat']) ?>
                    </p>

                    <span class="badge-kategori">
                        <?= htmlspecialchars($d['kategori_karya']) ?>
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
        function filterKarya(kategori_semua, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filter berdasarkan bungkus link kartu
            const links = document.querySelectorAll('.card-karya-link');
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