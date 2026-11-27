<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login_user.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/media_helpers.php';

$db = new Database();
$categories = ['foto', 'video'];

if (isset($_POST['save_dokumentasi'])) {
    $id = isset($_POST['id_dokumentasi']) ? (int) $_POST['id_dokumentasi'] : 0;
    $existingMedia = isset($_POST['media_lama']) && is_string($_POST['media_lama'])
        ? $_POST['media_lama']
        : '';
    $category = strtolower(trim($_POST['kategori_dokumentasi'] ?? ''));
    if (!in_array($category, $categories, true)) {
        header('Location: kelola_dokumentasi.php?status=invalid');
        exit;
    }

    $hasNewMedia = isset($_FILES['file_media']['error'])
        && $_FILES['file_media']['error'] !== UPLOAD_ERR_NO_FILE;
    if (!$hasNewMedia) {
        $extension = strtolower(pathinfo((string) parse_url($existingMedia, PHP_URL_PATH), PATHINFO_EXTENSION));
        $existingIsVideo = in_array($extension, ['mp4', 'webm'], true);
        if (($category === 'video' && $existingMedia === '') || ($existingMedia !== '' && ($category === 'video') !== $existingIsVideo)) {
            header('Location: kelola_dokumentasi.php?status=category_media_error');
            exit;
        }
    }

    $media = save_public_upload(
        $_FILES['file_media'] ?? [],
        'dokumentasi',
        $category === 'video',
        $existingMedia
    );
    if ($media === false) {
        header('Location: kelola_dokumentasi.php?status=upload_error');
        exit;
    }
    if ($media === '') {
        $media = 'assets/img/instagram/ruang-lain-poster.webp';
    }

    $values = [
        'judul_dokumentasi' => trim($_POST['judul_dokumentasi'] ?? ''),
        'kategori_dokumentasi' => $category,
        'tanggal_kegiatan' => $_POST['tanggal_kegiatan'] ?? '',
        'file_media' => $media,
    ];
    if (in_array('', [$values['judul_dokumentasi'], $values['tanggal_kegiatan']], true)) {
        header('Location: kelola_dokumentasi.php?status=invalid');
        exit;
    }

    if ($id > 0) {
        $values['id_dokumentasi'] = $id;
        $statement = $db->pdo->prepare(
            'UPDATE dokumentasi SET judul_dokumentasi = :judul_dokumentasi,
             kategori_dokumentasi = :kategori_dokumentasi, tanggal_kegiatan = :tanggal_kegiatan,
             file_media = :file_media WHERE id_dokumentasi = :id_dokumentasi'
        );
        $statement->execute($values);
        header('Location: kelola_dokumentasi.php?status=updated');
    } else {
        $statement = $db->pdo->prepare(
            'INSERT INTO dokumentasi
             (judul_dokumentasi, kategori_dokumentasi, tanggal_kegiatan, file_media)
             VALUES (:judul_dokumentasi, :kategori_dokumentasi, :tanggal_kegiatan, :file_media)'
        );
        $statement->execute($values);
        header('Location: kelola_dokumentasi.php?status=added');
    }
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $statement = $db->pdo->prepare('SELECT file_media FROM dokumentasi WHERE id_dokumentasi = :id');
    $statement->execute(['id' => $id]);
    $media = $statement->fetchColumn();
    $delete = $db->pdo->prepare('DELETE FROM dokumentasi WHERE id_dokumentasi = :id');
    $delete->execute(['id' => $id]);
    if (is_string($media)) {
        remove_public_upload($media, 'dokumentasi');
    }
    header('Location: kelola_dokumentasi.php?status=deleted');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $statement = $db->pdo->prepare('SELECT * FROM dokumentasi WHERE id_dokumentasi = :id');
    $statement->execute(['id' => (int) $_GET['edit']]);
    $editData = $statement->fetch() ?: null;
}
$dokumentasi = $db->pdo->query(
    'SELECT * FROM dokumentasi ORDER BY tanggal_kegiatan DESC, id_dokumentasi DESC'
)->fetchAll();

$messages = [
    'added' => 'Dokumentasi berhasil ditambahkan.',
    'updated' => 'Perubahan dokumentasi berhasil disimpan.',
    'deleted' => 'Dokumentasi berhasil dihapus.',
    'upload_error' => 'Unggahan ditolak. Foto: JPG, PNG, WebP maks. 10 MB. Video: MP4/WebM maks. 100 MB.',
    'invalid' => 'Lengkapi judul, kategori, dan tanggal dengan benar.',
    'category_media_error' => 'Kategori media tidak sesuai dengan file saat ini. Unggah file baru untuk mengganti jenis media.',
];
$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dokumentasi - KATASAPA</title>
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
            <a href="kelola_kegiatan.php">Kelola Kegiatan</a>
            <a href="kelola_dokumentasi.php" class="active">Kelola Dokumentasi</a>
            <a href="../dokumentasi.php" target="_blank" rel="noopener noreferrer" class="btn-lihat-web">Lihat dokumentasi publik</a>
            <a href="../index.php" class="btn-logout">Kembali ke beranda</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>Kelola Dokumentasi</h1>
                <p>Kelola foto dan video kegiatan KATASAPA.</p>
            </div>
            <div class="stat-box">
                <span class="stat-label">Total dokumentasi</span>
                <span class="stat-number"><?= count($dokumentasi); ?></span>
            </div>
        </header>
        <?php if (isset($messages[$status])): ?>
            <div class="alert-box" role="status"><?= htmlspecialchars($messages[$status], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <section class="admin-card">
            <h2 class="admin-card-title"><?= $editData ? 'Edit Dokumentasi' : 'Tambah Dokumentasi'; ?></h2>
            <form action="kelola_dokumentasi.php" method="post" enctype="multipart/form-data" class="admin-form">
                <?php if ($editData): ?>
                    <input type="hidden" name="id_dokumentasi" value="<?= (int) $editData['id_dokumentasi']; ?>">
                    <input type="hidden" name="media_lama" value="<?= htmlspecialchars($editData['file_media'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="judul-dokumentasi">Judul</label>
                        <input id="judul-dokumentasi" type="text" name="judul_dokumentasi" value="<?= htmlspecialchars($editData['judul_dokumentasi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori-dokumentasi">Jenis media</label>
                        <select id="kategori-dokumentasi" name="kategori_dokumentasi" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category; ?>" <?= ($editData['kategori_dokumentasi'] ?? 'foto') === $category ? 'selected' : ''; ?>><?= ucfirst($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal-dokumentasi">Tanggal kegiatan</label>
                        <input id="tanggal-dokumentasi" type="date" name="tanggal_kegiatan" value="<?= htmlspecialchars($editData['tanggal_kegiatan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="file-media">Unggah foto atau video</label>
                    <input id="file-media" type="file" name="file_media" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm">
                    <small class="help-text">Foto maks. 10 MB; video MP4/WebM maks. 100 MB. Kosongkan saat edit untuk mempertahankan file saat ini.</small>
                    <?php if (!empty($editData['file_media'])): ?>
                        <small class="help-text">File saat ini: <?= htmlspecialchars($editData['file_media'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-actions">
                    <button type="submit" name="save_dokumentasi" class="btn-primary-gold">Simpan dokumentasi</button>
                </div>
            </form>
        </section>

        <section class="admin-card">
            <h2 class="admin-card-title">Daftar dokumentasi</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>No.</th><th>Media</th><th>Judul</th><th>Jenis</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($dokumentasi as $index => $row): ?>
                        <?php
                        $media = $row['file_media'] ?: 'assets/img/instagram/ruang-lain-poster.webp';
                        if (preg_match('/^https?:\/\//i', $media)) {
                            $mediaUrl = $media;
                        } elseif (strpos($media, 'assets/') === 0 || strpos($media, 'uploads/') === 0) {
                            $mediaUrl = '../' . $media;
                        } else {
                            $mediaUrl = '../uploads/dokumentasi/' . ltrim($media, '/');
                        }
                        ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td>
                                <?php if (strtolower($row['kategori_dokumentasi']) === 'video'): ?>
                                    <video class="admin-thumb" src="<?= htmlspecialchars($mediaUrl, ENT_QUOTES, 'UTF-8'); ?>" controls></video>
                                <?php else: ?>
                                    <img class="admin-thumb" src="<?= htmlspecialchars($mediaUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy">
                                <?php endif; ?>
                            </td>
                            <td><strong class="table-judul"><?= htmlspecialchars($row['judul_dokumentasi'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><span class="badge-kategori"><?= htmlspecialchars(ucfirst($row['kategori_dokumentasi']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?= htmlspecialchars($row['tanggal_kegiatan'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="aksi-buttons">
                                <a class="btn-edit" href="kelola_dokumentasi.php?edit=<?= (int) $row['id_dokumentasi']; ?>">Edit</a>
                                <a class="btn-hapus" href="kelola_dokumentasi.php?hapus=<?= (int) $row['id_dokumentasi']; ?>" onclick="return confirm('Hapus dokumentasi ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$dokumentasi): ?><tr><td colspan="6" class="empty-row">Belum ada dokumentasi.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
