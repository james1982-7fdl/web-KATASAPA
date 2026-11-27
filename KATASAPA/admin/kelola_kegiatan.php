<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_user.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/media_helpers.php';

$db = new Database();
$categories = ['Workshop', 'Diskusi', 'Pertunjukan', 'Lainnya'];

if (isset($_POST['save_kegiatan'])) {
    $id = isset($_POST['id_kegiatan']) ? (int) $_POST['id_kegiatan'] : 0;
    $existingPoster = isset($_POST['poster_lama']) && is_string($_POST['poster_lama'])
        ? $_POST['poster_lama']
        : '';
    $poster = save_public_upload($_FILES['poster'] ?? [], 'kegiatan', false, $existingPoster);

    if ($poster === false) {
        header('Location: kelola_kegiatan.php?status=upload_error');
        exit;
    }

    $poster = $poster !== '' ? $poster : 'assets/img/instagram/ruang-lain-workshop.webp';
    $values = [
        'judul_kegiatan' => trim($_POST['judul_kegiatan'] ?? ''),
        'kategori_kegiatan' => trim($_POST['kategori_kegiatan'] ?? ''),
        'tanggal_kegiatan' => $_POST['tanggal_kegiatan'] ?? '',
        'waktu_kegiatan' => trim($_POST['waktu_kegiatan'] ?? ''),
        'waktu_selesai' => trim($_POST['waktu_selesai'] ?? ''),
        'lokasi_kegiatan' => trim($_POST['lokasi_kegiatan'] ?? ''),
        'deskripsi_singkat' => trim($_POST['deskripsi_singkat'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? ''),
        'poster' => $poster,
    ];

    if (in_array('', [$values['judul_kegiatan'], $values['kategori_kegiatan'], $values['tanggal_kegiatan'], $values['lokasi_kegiatan'], $values['deskripsi_singkat'], $values['deskripsi']], true)) {
        header('Location: kelola_kegiatan.php?status=invalid');
        exit;
    }

    if ($id > 0) {
        $values['id_kegiatan'] = $id;
        $statement = $db->pdo->prepare(
            'UPDATE kegiatan2 SET judul_kegiatan = :judul_kegiatan,
             kategori_kegiatan = :kategori_kegiatan, tanggal_kegiatan = :tanggal_kegiatan,
             waktu_kegiatan = :waktu_kegiatan, waktu_selesai = :waktu_selesai,
             lokasi_kegiatan = :lokasi_kegiatan, deskripsi_singkat = :deskripsi_singkat,
             deskripsi = :deskripsi, poster = :poster WHERE id_kegiatan = :id_kegiatan'
        );
        $statement->execute($values);
        header('Location: kelola_kegiatan.php?status=updated');
    } else {
        $statement = $db->pdo->prepare(
            'INSERT INTO kegiatan2
             (judul_kegiatan, kategori_kegiatan, tanggal_kegiatan, waktu_kegiatan,
              waktu_selesai, lokasi_kegiatan, deskripsi_singkat, deskripsi, poster)
             VALUES (:judul_kegiatan, :kategori_kegiatan, :tanggal_kegiatan, :waktu_kegiatan,
              :waktu_selesai, :lokasi_kegiatan, :deskripsi_singkat, :deskripsi, :poster)'
        );
        $statement->execute($values);
        header('Location: kelola_kegiatan.php?status=added');
    }
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $statement = $db->pdo->prepare('SELECT poster FROM kegiatan2 WHERE id_kegiatan = :id');
    $statement->execute(['id' => $id]);
    $poster = $statement->fetchColumn();
    $delete = $db->pdo->prepare('DELETE FROM kegiatan2 WHERE id_kegiatan = :id');
    $delete->execute(['id' => $id]);
    if (is_string($poster)) {
        remove_public_upload($poster, 'kegiatan');
    }
    header('Location: kelola_kegiatan.php?status=deleted');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $statement = $db->pdo->prepare('SELECT * FROM kegiatan2 WHERE id_kegiatan = :id');
    $statement->execute(['id' => (int) $_GET['edit']]);
    $editData = $statement->fetch() ?: null;
}
$kegiatan = $db->pdo->query(
    'SELECT * FROM kegiatan2 ORDER BY tanggal_kegiatan DESC, id_kegiatan DESC'
)->fetchAll();

$messages = [
    'added' => 'Kegiatan berhasil ditambahkan.',
    'updated' => 'Perubahan kegiatan berhasil disimpan.',
    'deleted' => 'Kegiatan berhasil dihapus.',
    'upload_error' => 'Unggahan ditolak. Gunakan gambar JPG, PNG, atau WebP hingga 10 MB.',
    'invalid' => 'Lengkapi semua data wajib sebelum menyimpan.',
];
$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kegiatan - KATASAPA</title>
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
            <a href="dashboard_admin.php">Dashboard</a>
            <a href="kelola_karya.php">Kelola Karya</a>
            <a href="kelola_kegiatan.php" class="active">Kelola Kegiatan</a>
            <a href="kelola_dokumentasi.php">Kelola Dokumentasi</a>
            <a href="../kegiatan.php" target="_blank" rel="noopener noreferrer" class="btn-lihat-web">Lihat halaman kegiatan</a>
            <a href="../index.php" class="btn-logout">Kembali ke beranda</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>Kelola Kegiatan</h1>
                <p>Tambah, perbarui, dan hapus agenda KATASAPA.</p>
            </div>
            <div class="stat-box">
                <span class="stat-label">Total kegiatan</span>
                <span class="stat-number"><?= count($kegiatan); ?></span>
            </div>
        </header>
        <?php if (isset($messages[$status])): ?>
            <div class="alert-box" role="status"><?= htmlspecialchars($messages[$status], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <section class="admin-card">
            <h2 class="admin-card-title"><?= $editData ? 'Edit Kegiatan' : 'Tambah Kegiatan'; ?></h2>
            <form action="kelola_kegiatan.php" method="post" enctype="multipart/form-data" class="admin-form">
                <?php if ($editData): ?>
                    <input type="hidden" name="id_kegiatan" value="<?= (int) $editData['id_kegiatan']; ?>">
                    <input type="hidden" name="poster_lama" value="<?= htmlspecialchars($editData['poster'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="judul-kegiatan">Judul kegiatan</label>
                        <input id="judul-kegiatan" type="text" name="judul_kegiatan" value="<?= htmlspecialchars($editData['judul_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori-kegiatan">Kategori</label>
                        <select id="kategori-kegiatan" name="kategori_kegiatan" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" <?= ($editData['kategori_kegiatan'] ?? '') === $category ? 'selected' : ''; ?>><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal-kegiatan">Tanggal</label>
                        <input id="tanggal-kegiatan" type="date" name="tanggal_kegiatan" value="<?= htmlspecialchars($editData['tanggal_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="waktu-kegiatan">Waktu mulai</label>
                        <input id="waktu-kegiatan" type="time" name="waktu_kegiatan" value="<?= htmlspecialchars($editData['waktu_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="waktu-selesai">Waktu selesai</label>
                        <input id="waktu-selesai" type="time" name="waktu_selesai" value="<?= htmlspecialchars($editData['waktu_selesai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lokasi-kegiatan">Lokasi</label>
                        <input id="lokasi-kegiatan" type="text" name="lokasi_kegiatan" value="<?= htmlspecialchars($editData['lokasi_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ringkasan-kegiatan">Ringkasan untuk kartu</label>
                    <textarea id="ringkasan-kegiatan" name="deskripsi_singkat" rows="3" required><?= htmlspecialchars($editData['deskripsi_singkat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="deskripsi-kegiatan">Deskripsi lengkap</label>
                    <textarea id="deskripsi-kegiatan" name="deskripsi" rows="6" required><?= htmlspecialchars($editData['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="poster-kegiatan">Poster kegiatan (JPG, PNG, WebP; maks. 10 MB)</label>
                    <input id="poster-kegiatan" type="file" name="poster" accept="image/jpeg,image/png,image/webp">
                    <?php if (!empty($editData['poster'])): ?>
                        <small class="help-text">Gambar saat ini: <?= htmlspecialchars($editData['poster'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-actions">
                    <button type="submit" name="save_kegiatan" class="btn-primary-gold">Simpan kegiatan</button>
                </div>
            </form>
        </section>

        <section class="admin-card">
            <h2 class="admin-card-title">Daftar kegiatan</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>No.</th><th>Poster</th><th>Kegiatan</th><th>Tanggal</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($kegiatan as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><img class="admin-thumb" src="../<?= htmlspecialchars($row['poster'] ?: 'assets/img/instagram/ruang-lain-workshop.webp', ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy"></td>
                            <td><strong class="table-judul"><?= htmlspecialchars($row['judul_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></strong><span class="badge-kategori"><?= htmlspecialchars($row['kategori_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?= htmlspecialchars($row['tanggal_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($row['lokasi_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="aksi-buttons">
                                <a class="btn-edit" href="kelola_kegiatan.php?edit=<?= (int) $row['id_kegiatan']; ?>">Edit</a>
                                <a class="btn-hapus" href="kelola_kegiatan.php?hapus=<?= (int) $row['id_kegiatan']; ?>" onclick="return confirm('Hapus kegiatan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$kegiatan): ?><tr><td colspan="6" class="empty-row">Belum ada data kegiatan.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
