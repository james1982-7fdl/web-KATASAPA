<?php
require_once __DIR__ . '/config/database.php';
$db = new Database();
$agendaStatement = $db->pdo->prepare(
    'SELECT * FROM kegiatan2
     WHERE tanggal_kegiatan >= CURRENT_DATE()
     ORDER BY tanggal_kegiatan ASC, id_kegiatan ASC
     LIMIT 4'
);
$agendaStatement->execute();
$agendaTerdekat = $agendaStatement->fetchAll();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KATASAPA - Komunitas Teater dan Sastra Perwira</title>
    
    <!-- Google Fonts: Playfair Display & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Menghubungkan ke file CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- ==========================================
         1. NAVBAR / HEADER
         ========================================== -->
    <header class="navbar">
        <div class="logo-container">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo Katasapa" class="logo-img" onerror="this.style.display='none'">
            <div class="logo-text">
                <h1>KATASAPA</h1>
                <p>Komunitas Teater dan Sastra Perwira</p>
            </div>
        </div>

        <nav>
            <ul>
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="tentang.php">Tentang Kami</a></li>
                <li><a href="kegiatan.php">Kegiatan</a></li>
                <li><a href="karya.php">Karya</a></li>
                <li><a href="dokumentasi.php">Dokumentasi</a></li>
            </ul>
        </nav>

        <a href="admin/login_user.php" class="btn-login">Admin</a>
    </header>

    <!-- ==========================================
         2. HERO SECTION (BANNER UTAMA)
         ========================================== -->
    <section id="beranda" class="hero">
        <div class="hero-content">
            <h2>KATASAPA</h2>
            <h3>Komunitas Teater dan Sastra Perwira</h3>
            <p>Ruang tumbuh bagi kata, panggung, dan manusia.</p>
            <a href="kegiatan.php" class="btn-hero">Jelajahi Kegiatan &rarr;</a>
        </div>
    </section>

    <!-- ==========================================
         3. KOTAK FITUR / MENU UTAMA (4 GRID)
         ========================================== -->
    <section class="features">
        <a href="kegiatan.php" class="feature-item">
            <h4>Kegiatan</h4>
            <p>Workshop, pertunjukan, diskusi, dan agenda lainnya.</p>
        </a>
        <a href="karya.php" class="feature-item">
            <h4>Karya</h4>
            <p>Puisi, cerpen, esai, dan naskah teater.</p>
        </a>
        <a href="dokumentasi.php" class="feature-item">
            <h4>Dokumentasi</h4>
            <p>Arsip kegiatan dalam bentuk foto dan video.</p>
        </a>
        <a href="tentang.php" class="feature-item">
            <h4>Tentang Kami</h4>
            <p>Mengenal lebih dekat KATASAPA.</p>
        </a>
    </section>

    <!-- ==========================================
         4. AGENDA TERDEKAT SECTION
         ========================================== -->
    <section id="kegiatan" class="agenda-section">
        <div class="section-title">
            <h3>Agenda Terdekat</h3>
            <a href="kegiatan.php">Lihat Semua &rarr;</a>
        </div>

        <div class="agenda-grid">
            <?php if ($agendaTerdekat): ?>
                <?php foreach ($agendaTerdekat as $agenda): ?>
                    <?php
                    $agendaDate = strtotime($agenda['tanggal_kegiatan']);
                    $agendaPoster = !empty($agenda['poster'])
                        ? $agenda['poster']
                        : 'assets/img/instagram/ruang-lain-workshop.webp';
                    ?>
                    <a class="agenda-link" href="detail_kegiatan.php?id=<?= (int) $agenda['id_kegiatan']; ?>">
                        <article class="card-agenda">
                            <img class="card-img" src="<?= htmlspecialchars($agendaPoster, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($agenda['judul_kegiatan'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/img/instagram/ruang-lain-workshop.webp';">
                            <div class="card-body">
                                <div class="date-box">
                                    <span class="day"><?= date('d', $agendaDate); ?></span>
                                    <span class="month"><?= date('M', $agendaDate); ?></span>
                                </div>
                                <div class="card-info">
                                    <h4><?= htmlspecialchars($agenda['judul_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p><?= htmlspecialchars($agenda['lokasi_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <span class="badge"><?= htmlspecialchars($agenda['kategori_kegiatan'] ?? 'Kegiatan', ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        </article>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="agenda-empty">Belum ada agenda mendatang. Silakan cek kembali nanti.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ==========================================
         5. TENTANG & QUOTE SECTION
         ========================================== -->
    <section id="tentang" class="about-quote-section">
        <div class="about-text">
            <h3>Tentang KATASAPA</h3>
            <p>
                KATASAPA (Komunitas Teater dan Sastra Perwira) berdiri pada tahun 2016 di Purbalingga, 
                berawal dari sekelompok pemuda yang memiliki kegemaran berteater dan menulis. 
                KATASAPA kini menjadi ruang berkarya, berbagi, dan belajar bagi siapa saja yang mencintai teater dan sastra.
            </p>
            <a href="tentang.php" class="btn-dark">Baca Selengkapnya &rarr;</a>
        </div>

        <aside class="quote-card">
            <p>"Seni dan sastra bukan hanya tentang keindahan, tetapi juga tentang keberanian untuk menyuarakan yang tak terlihat."</p>
            <span>&mdash; KATASAPA</span>
        </aside>
    </section>

    <!-- ==========================================
         6. FOOTER
         ========================================== -->
    <footer>
        <div>
            <strong>KATASAPA</strong> &copy; 2026 KATASAPA. Semua Hak Dilindungi.
        </div>
        <div>
            Instagram: <a href="https://www.instagram.com/katasapa.pbg/" target="_blank" rel="noopener noreferrer" style="color: #D4AF37;">@katasapa.pbg</a>
        </div>
    </footer>

</body>
</html>