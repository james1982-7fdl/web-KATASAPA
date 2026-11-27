<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$id = isset($_GET['id']) && is_scalar($_GET['id']) ? (int) $_GET['id'] : 0;
$statement = $db->pdo->prepare(
    'SELECT * FROM karya WHERE id_karya = :id LIMIT 1'
);
$statement->execute(['id' => $id]);
$karya = $statement->fetch();

if (!$karya) {
    http_response_code(404);
}

$judul = $karya['judul_karya'] ?? 'Karya tidak ditemukan';
$poster = !empty($karya['poster_karya'])
    ? $karya['poster_karya']
    : 'assets/img/instagram/ruang-lain-poster.webp';
$deskripsi = $karya['deskripsi'] ?? $karya['deskripsi_singkat'] ?? '';
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
    <link rel="stylesheet" href="assets/css/desain_karya.css">
</head>
<body class="halaman-karya">
    <header class="navbar">
        <a class="logo-container" href="index.php">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo KATASAPA" class="logo-img">
            <span class="logo-text">KATASAPA</span>
        </a>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php">Tentang Kami</a></li>
                <li><a href="kegiatan.php">Kegiatan</a></li>
                <li><a href="karya.php" class="active">Karya</a></li>
                <li><a href="dokumentasi.php">Dokumentasi</a></li>
            </ul>
        </nav>
        <a href="admin/login_user.php" class="btn-login">Admin</a>
    </header>

    <main class="content-container detail-page">
        <div class="detail-main">
            <p class="back-link"><a href="karya.php">&larr; Kembali ke karya</a></p>
            <?php if ($karya): ?>
                <article class="detail-card">
                    <img class="detail-img" src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-poster.webp';">
                    <div class="detail-body">
                        <span class="badge-kategori"><?= htmlspecialchars($karya['kategori_karya'] ?? 'Karya', ENT_QUOTES, 'UTF-8'); ?></span>
                        <h1 class="detail-judul"><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <dl class="meta-info-grid">
                            <div class="meta-item">
                                <dt class="meta-label">Penulis</dt>
                                <dd class="meta-val"><?= htmlspecialchars($karya['penulis'] ?? 'KATASAPA', ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <div class="meta-item">
                                <dt class="meta-label">Tanggal publikasi</dt>
                                <dd class="meta-val"><?= htmlspecialchars($karya['tanggal_publikasi'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                        </dl>
                        <section class="detail-deskripsi-section">
                            <h2>Deskripsi karya</h2>
                            <p><?= nl2br(htmlspecialchars($deskripsi, ENT_QUOTES, 'UTF-8')); ?></p>
                        </section>
                    </div>
                </article>
            <?php else: ?>
                <section class="error-not-found">
                    <h1>Karya tidak ditemukan</h1>
                    <p>Data karya tidak tersedia atau sudah dihapus.</p>
                    <a class="btn-login" href="karya.php">Lihat karya</a>
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
