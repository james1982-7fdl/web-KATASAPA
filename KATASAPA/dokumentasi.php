<?php
require_once 'config/database.php';

$db = new Database();

$filter = isset($_GET['kategori']) && is_string($_GET['kategori'])
    ? strtolower(trim($_GET['kategori']))
    : 'semua';

if ($filter === 'foto' || $filter === 'video') {
    $stmt = $db->pdo->prepare(
        'SELECT * FROM dokumentasi
         WHERE LOWER(kategori_dokumentasi) = :kategori
         ORDER BY tanggal_kegiatan DESC, id_dokumentasi DESC'
    );
    $stmt->execute(['kategori' => $filter]);
    $rows = $stmt->fetchAll();
} else {
    $filter = 'semua';
    $rows = $db->pdo
        ->query('SELECT * FROM dokumentasi ORDER BY tanggal_kegiatan DESC, id_dokumentasi DESC')
        ->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-banner {
            background: linear-gradient(135deg, rgba(25,27,25,0.88), rgba(54,58,54,0.78)), url('assets/img/instagram/ruang-lain-poster.webp') center/cover no-repeat;
            padding: 4rem 5%;
            border-bottom: 2px solid #363A36;
            animation: banner-in 0.65s ease both;
        }
        .page-banner h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.25rem;
            color: #FFFFFF;
        }
        .page-banner p {
            color: #8D8E8E;
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        .doc-container {
            padding: 3rem 5% 5rem 5%;
        }
        .filter-bar {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.45rem 1.3rem;
            border-radius: 20px;
            font-size: 0.85rem;
            background-color: #272927;
            color: #C0C4C4;
            border: 1px solid #363A36;
            transition: all 0.3s ease;
        }
        .filter-btn:hover,
        .filter-btn.active {
            background-color: #4B4F4B;
            color: #FFFFFF;
            border-color: #8D8E8E;
        }
        .navbar {
            animation: navbar-in 0.55s ease both;
            transition: box-shadow 0.25s ease, background-color 0.25s ease;
        }
        .navbar nav a {
            position: relative;
            display: inline-block;
            transition: color 0.25s ease, transform 0.25s ease;
        }
        .navbar nav a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -7px;
            width: 100%;
            height: 2px;
            background: #D4AF37;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.25s ease;
        }
        .navbar nav a:hover,
        .navbar nav a.active {
            transform: translateY(-2px);
        }
        .navbar nav a:hover::after,
        .navbar nav a.active::after {
            transform: scaleX(1);
        }

        .doc-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .doc-card {
            background-color: #272927;
            border: 1px solid #363A36;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .doc-card:hover {
            transform: translateY(-4px);
            border-color: #696969;
        }
        .doc-media {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
            background-color: #191B19;
        }
        .doc-info {
            padding: 1.2rem;
        }
        .doc-info h4 {
            color: #FFFFFF;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }
        .doc-info span {
            color: #8D8E8E;
            font-size: 0.72rem;
        }
        @keyframes navbar-in {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes banner-in {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media (max-width: 900px) {
            .doc-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .doc-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo-container">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo Katasapa" class="logo-img">
            <div class="logo-text">
                <h1>KATASAPA</h1>
                <p>Komunitas Teater dan Sastra Perwira</p>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php">Tentang Kami</a></li>
                <li><a href="kegiatan.php">Kegiatan</a></li>
                <li><a href="karya.php">Karya</a></li>
                <li><a href="dokumentasi.php" class="active">Dokumentasi</a></li>
            </ul>
        </nav>
        <div class="nav-btn">
            <a href="admin/login_user.php" class="btn-login">Login</a>
        </div>
    </header>

    <section class="page-banner">
        <h2>Dokumentasi</h2>
        <p>Jejak kegiatan kami dalam bentuk foto dan video pementasan maupun diskusi.</p>
    </section>

    <main class="doc-container">
        <div class="filter-bar">
            <a href="dokumentasi.php?kategori=semua" class="filter-btn <?= $filter === 'semua' ? 'active' : '' ?>">Semua</a>
            <a href="dokumentasi.php?kategori=foto" class="filter-btn <?= $filter === 'foto' ? 'active' : '' ?>">Foto</a>
            <a href="dokumentasi.php?kategori=video" class="filter-btn <?= $filter === 'video' ? 'active' : '' ?>">Video</a>
        </div>

        <div class="doc-grid">
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $mediaRaw = isset($row['file_media']) && is_string($row['file_media'])
                        ? trim($row['file_media'])
                        : '';
                    if ($mediaRaw === '') {
                        $mediaSrc = 'assets/img/instagram/ruang-lain-poster.webp';
                    } elseif (preg_match('/^https?:\/\//i', $mediaRaw)) {
                        $mediaSrc = $mediaRaw;
                    } elseif (strpos($mediaRaw, 'assets/') === 0 || strpos($mediaRaw, 'uploads/') === 0) {
                        $mediaSrc = $mediaRaw;
                    } else {
                        $mediaSrc = "uploads/dokumentasi/" . $mediaRaw;
                    }
                    $mediaExtension = strtolower(pathinfo((string) parse_url($mediaSrc, PHP_URL_PATH), PATHINFO_EXTENSION));
                    ?>
                    <div class="doc-card">
                        <?php if (strtolower($row['kategori_dokumentasi']) === 'video'): ?>
                            <video class="doc-media" controls>
                                <source src="<?= htmlspecialchars($mediaSrc, ENT_QUOTES, 'UTF-8'); ?>" type="<?= $mediaExtension === 'webm' ? 'video/webm' : 'video/mp4'; ?>">
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($mediaSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($row['judul_dokumentasi'], ENT_QUOTES, 'UTF-8'); ?>" class="doc-media" loading="lazy" onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-poster.webp';">
                        <?php endif; ?>
                        <div class="doc-info">
                            <h4><?= htmlspecialchars($row['judul_dokumentasi'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <span><?= date('d M Y', strtotime($row['tanggal_kegiatan'])); ?> &bull; <?= htmlspecialchars($row['kategori_dokumentasi'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #8D8E8E;">Belum ada data dokumentasi yang diunggah.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p><strong>KATASAPA</strong> &copy; 2026 KATASAPA. Semua Hak Dilindungi.</p>
        <p>Instagram: <a href="https://www.instagram.com/katasapa.pbg/" target="_blank" rel="noopener noreferrer">@katasapa.pbg</a></p>
    </footer>

</body>
</html>