<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_user.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
$db = new Database();

$counts = [
    'kegiatan' => (int) $db->pdo->query('SELECT COUNT(*) FROM kegiatan2')->fetchColumn(),
    'karya' => (int) $db->pdo->query('SELECT COUNT(*) FROM karya')->fetchColumn(),
    'dokumentasi' => (int) $db->pdo->query('SELECT COUNT(*) FROM dokumentasi')->fetchColumn(),
];
$agenda = $db->pdo->query(
    'SELECT id_kegiatan, judul_kegiatan, kategori_kegiatan, tanggal_kegiatan
     FROM kegiatan2
     WHERE tanggal_kegiatan >= CURRENT_DATE()
     ORDER BY tanggal_kegiatan ASC, id_kegiatan ASC
     LIMIT 5'
)->fetchAll();
$recentKarya = $db->pdo->query(
    'SELECT id_karya, judul_karya, kategori_karya, tanggal_publikasi
     FROM karya
     ORDER BY tanggal_publikasi DESC, id_karya DESC
     LIMIT 5'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <a href="dashboard_admin.php"><h2>KATASAPA</h2></a>
            <span>Panel Admin Kesenian</span>
        </div>
        <nav class="sidebar-menu">
            <a href="dashboard_admin.php" class="active">Dashboard</a>
            <a href="kelola_karya.php">Kelola Karya</a>
            <a href="kelola_kegiatan.php">Kelola Kegiatan</a>
            <a href="kelola_dokumentasi.php">Kelola Dokumentasi</a>
            <a href="../index.php" target="_blank" rel="noopener noreferrer" class="btn-lihat-web">Lihat website</a>
            <a href="../index.php" class="btn-logout">Kembali ke beranda</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>Dashboard KATASAPA</h1>
                <p>Ringkasan konten dan agenda mendatang.</p>
            </div>
        </header>

        <section class="stats-grid" aria-label="Ringkasan data">
            <a href="kelola_kegiatan.php" class="stat-card">
                <span class="stat-label">Total kegiatan</span>
                <strong class="stat-number"><?= $counts['kegiatan']; ?></strong>
                <span class="stat-hint">Kelola agenda &rarr;</span>
            </a>
            <a href="kelola_karya.php" class="stat-card">
                <span class="stat-label">Total karya</span>
                <strong class="stat-number"><?= $counts['karya']; ?></strong>
                <span class="stat-hint">Kelola arsip &rarr;</span>
            </a>
            <a href="kelola_dokumentasi.php" class="stat-card">
                <span class="stat-label">Total dokumentasi</span>
                <strong class="stat-number"><?= $counts['dokumentasi']; ?></strong>
                <span class="stat-hint">Kelola media &rarr;</span>
            </a>
        </section>

        <div class="dashboard-columns">
            <section class="admin-card">
                <h2 class="admin-card-title">Agenda terdekat</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Kategori</th></tr></thead>
                        <tbody>
                        <?php foreach ($agenda as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="kelola_kegiatan.php?edit=<?= (int) $row['id_kegiatan']; ?>"><?= htmlspecialchars($row['judul_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                <td><?= htmlspecialchars($row['kategori_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$agenda): ?><tr><td colspan="3" class="empty-row">Belum ada agenda mendatang.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="admin-card">
                <h2 class="admin-card-title">Karya terbaru</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentKarya as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal_publikasi'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="kelola_karya.php?edit=<?= (int) $row['id_karya']; ?>"><?= htmlspecialchars($row['judul_karya'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                                <td><?= htmlspecialchars($row['kategori_karya'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentKarya): ?><tr><td colspan="3" class="empty-row">Belum ada karya.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
