<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$id = isset($_GET['id']) && is_scalar($_GET['id']) ? (int) $_GET['id'] : 0;
$statement = $db->pdo->prepare(
    'SELECT * FROM kegiatan2 WHERE id_kegiatan = :id LIMIT 1'
);
$statement->execute(['id' => $id]);
$kegiatan = $statement->fetch();

if (!$kegiatan) {
    http_response_code(404);
}

$judul = $kegiatan['judul_kegiatan'] ?? 'Kegiatan tidak ditemukan';
$poster = !empty($kegiatan['poster'])
    ? $kegiatan['poster']
    : 'assets/img/instagram/ruang-lain-workshop.webp';
$deskripsi = $kegiatan['deskripsi'] ?? $kegiatan['deskripsi_singkat'] ?? '';
$tanggal = !empty($kegiatan['tanggal_kegiatan'])
    ? date('d/m/Y', strtotime($kegiatan['tanggal_kegiatan']))
    : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8'); ?> - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/kegiatan.css">
</head>
<body class="halaman-kegiatan">
    <header class="navbar">
        <a class="logo-container" href="index.php">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo KATASAPA" class="logo-img">
            <span class="logo-text">KATASAPA</span>
        </a>
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

    <main class="content-container detail-page">
        <div class="detail-main">
            <p class="back-link"><a href="kegiatan.php">&larr; Kembali ke kegiatan</a></p>
            <?php if ($kegiatan): ?>
                <article class="detail-card">
                    <img class="detail-img" src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-workshop.webp';">
                    <div class="detail-body">
                        <span class="badge-kategori"><?= htmlspecialchars($kegiatan['kategori_kegiatan'] ?? 'Kegiatan', ENT_QUOTES, 'UTF-8'); ?></span>
                        <h1 class="detail-judul"><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <dl class="meta-info-grid">
                            <div class="meta-item">
                                <dt class="meta-label">Tanggal</dt>
                                <dd class="meta-val"><?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <div class="meta-item">
                                <dt class="meta-label">Waktu</dt>
                                <dd class="meta-val">
                                    <?= htmlspecialchars($kegiatan['waktu_kegiatan'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if (!empty($kegiatan['waktu_selesai'])): ?>
                                        - <?= htmlspecialchars($kegiatan['waktu_selesai'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endif; ?>
                                </dd>
                            </div>
                            <div class="meta-item">
                                <dt class="meta-label">Lokasi</dt>
                                <dd class="meta-val"><?= htmlspecialchars($kegiatan['lokasi_kegiatan'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                        </dl>
                        <section class="detail-deskripsi-section">
                            <h2>Tentang kegiatan</h2>
                            <p><?= nl2br(htmlspecialchars($deskripsi, ENT_QUOTES, 'UTF-8')); ?></p>
                        </section>
                    </div>
                </article>
            <?php else: ?>
                <section class="error-not-found">
                    <h1>Kegiatan tidak ditemukan</h1>
                    <p>Data kegiatan tidak tersedia atau sudah dihapus.</p>
                    <a class="btn-login" href="kegiatan.php">Lihat kegiatan</a>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <span><strong>KATASAPA</strong> &copy; 2026</span>
        <a href="https://www.instagram.com/katasapa.pbg/" target="_blank" rel="noopener noreferrer">@katasapa.pbg</a>
    </footer>
</body>
</html>
