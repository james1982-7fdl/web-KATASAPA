<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("location: login_user.php");
    exit;
}

require_once '../config/database.php';
require_once __DIR__ . '/media_helpers.php';

$db = new Database();

// Fungsi upload poster
function uploadPoster($file, $posterLama = '')
{
    return save_public_upload($file, 'karya', false, $posterLama);
}


// PROSES TAMBAH KARYA
if (isset($_POST['tambah_karya'])) {

    $poster = uploadPoster($_FILES['poster_karya'] ?? []);

    if ($poster !== false) {
        $poster = $poster !== '' ? $poster : 'assets/img/instagram/ruang-lain-poster.webp';

        $data = [
            'judul_karya' => $_POST['judul_karya'],
            'penulis' => $_POST['penulis'],
            'kategori_karya' => $_POST['kategori_karya'],
            'deskripsi' => $_POST['deskripsi'],
            'file_karya' => '',
            'tanggal_publikasi' => $_POST['tanggal_publikasi'],
            'deskripsi_singkat' => $_POST['deskripsi_singkat'],
            'poster_karya' => $poster,
            'status_publikasi' => 'Draft'
        ];

        if ($db->insert('karya', $data)) {
            header("Location: kelola_karya.php?status=added");
            exit;
        }
    }

    header("Location: kelola_karya.php?status=error");
    exit;
}


// PROSES EDIT KARYA
if (isset($_POST['edit_karya'])) {

    $id = (int) $_POST['id_karya'];

    $poster = uploadPoster(
        $_FILES['poster_karya'] ?? [],
        $_POST['poster_lama'] ?? ''
    );

    if ($poster !== false) {

        $sql = "UPDATE karya SET
                judul_karya = ?,
                penulis = ?,
                kategori_karya = ?,
                deskripsi = ?,
                tanggal_publikasi = ?,
                deskripsi_singkat = ?,
                poster_karya = ?
                WHERE id_karya = ?";

        $hasil = $db->execute($sql, [
            $_POST['judul_karya'],
            $_POST['penulis'],
            $_POST['kategori_karya'],
            $_POST['deskripsi'],
            $_POST['tanggal_publikasi'],
            $_POST['deskripsi_singkat'],
            $poster,
            $id
        ]);

        header("Location: kelola_karya.php?status=" .
            ($hasil ? 'updated' : 'error'));
        exit;
    }

    header("Location: kelola_karya.php?status=error");
    exit;
}


// PROSES HAPUS KARYA
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];
    $posterStatement = $db->pdo->prepare('SELECT poster_karya FROM karya WHERE id_karya = ?');
    $posterStatement->execute([$id]);
    $posterLama = $posterStatement->fetchColumn();

    $hasil = $db->execute(
        "DELETE FROM karya WHERE id_karya = ?",
        [$id]
    );
    if ($hasil && is_string($posterLama)) {
        remove_public_upload($posterLama, 'karya');
    }

    header("Location: kelola_karya.php?status=" .
        ($hasil ? 'deleted' : 'error'));
    exit;
}


// PROSES PUBLIKASI KARYA
if (isset($_GET['publikasi'])) {

    $id = (int) $_GET['publikasi'];

    $hasil = $db->execute(
        "UPDATE karya SET status_publikasi = 'Terbit'
         WHERE id_karya = ? AND status_publikasi = 'Draft'",
        [$id]
    );

    header("Location: kelola_karya.php?status=" .
        ($hasil ? 'published' : 'error'));
    exit;
}


// MENGAMBIL DATA UNTUK TABEL
$karya = $db->sql(
    "SELECT * FROM karya ORDER BY id_karya DESC"
);

$total_karya = count($karya);


// MENGAMBIL DATA UNTUK EDIT
$edit_data = null;

if (isset($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $statement = $db->pdo->prepare('SELECT * FROM karya WHERE id_karya = ? LIMIT 1');
    $statement->execute([$id]);
    $edit_data = $statement->fetch() ?: null;
}
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
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
            <a href="./dashboard_admin.php" ><h2> KATASAPA</h2></a>
                <span>Panel Admin Kesenian</span>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard_admin.php">Dashboard</a>
                <a href="kelola_karya.php" class="active">Kelola Karya Sastra</a>
                <a href="kelola_kegiatan.php">Kelola Kegiatan</a>
                <a href="kelola_dokumentasi.php">Kelola Dokumentasi</a>
                <a href="../karya.php" target="_blank" class="btn-lihat-web">Lihat Halaman Karya &nearr;</a>
                <a href="../index.php" class="btn-logout">Kembali ke Beranda</a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <h1>Dashboard Pengelolaan Karya</h1>
                    <p>Tambah, ubah, atau hapus arsip puisi, cerpen, esai, dan naskah teater.</p>
                </div>
                <div class="stat-box">
                    <span class="stat-label">Total Karya Terbit</span>
                    <span class="stat-number"><?= $total_karya; ?></span>
                </div>
            </header>

            <!-- NOTIFIKASI -->
            <?php if (isset($_GET['status'])): ?>

<div class="alert-box">

    <?php
    if ($_GET['status'] == 'added') {
        echo "Karya berhasil ditambahkan dan disimpan sebagai Draft!";
    }

    elseif ($_GET['status'] == 'updated') {
        echo "Perubahan karya berhasil disimpan!";
    }

    elseif ($_GET['status'] == 'deleted') {
        echo "Karya berhasil dihapus!";
    }

    elseif ($_GET['status'] == 'published') {
        echo "Karya berhasil dipublikasikan!";
    }

    elseif ($_GET['status'] == 'error') {
        echo "Terjadi kesalahan. Periksa kembali data atau file yang diunggah!";
    }
    ?>

</div>

<?php endif; ?>

            <!-- FORM TAMBAH / EDIT (CREATE & UPDATE) -->
            <section class="admin-card">
                <h3 class="admin-card-title">
                    <?= $edit_data ? "Edit Karya: " . htmlspecialchars($edit_data['judul_karya']) : "Tambah Karya Baru"; ?>
                </h3>
                
                <form action="kelola_karya.php" method="POST" enctype="multipart/form-data" class="admin-form" onsubmit="return confirm('Yakin ingin menyimpan data karya ini?')">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_karya" value="<?= $edit_data['id_karya']; ?>">
                        <input type="hidden" name="poster_lama" value="<?= htmlspecialchars($edit_data['poster_karya']); ?>">                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kategori Karya</label>
                            <select name="kategori_karya" required>
                                <?php 
                                    $kategori_opsi = ['Puisi', 'Esai', 'Cerpen'];
                                    foreach ($kategori_opsi as $kat) {
                                        $selected = ($edit_data && $edit_data['kategori_karya'] == $kat) ? 'selected' : '';
                                        echo "<option value='$kat' $selected>$kat</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Judul Karya</label>
                            <input type="text" name="judul_karya" placeholder="Contoh: Hujan di Alun-Alun Perwira" 
                                   value="<?= $edit_data ? htmlspecialchars($edit_data['judul_karya']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi Lengkap Karya</label>
                            <textarea name="deskripsi" rows="5" required><?=$edit_data ? htmlspecialchars($edit_data['deskripsi']) : '';
                            ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Penulis / Divisi</label>
                            <input type="text" name="penulis" placeholder="Contoh: Chairil Anwar" 
                                   value="<?= $edit_data ? htmlspecialchars($edit_data['penulis']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Bulan & Tahun Terbit</label>
                            <input type="date" name="tanggal_publikasi" placeholder="Contoh: Nov 2026" 
                                   value="<?= $edit_data ? htmlspecialchars($edit_data['tanggal_publikasi']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cuplikan / Isi Singkat Karya</label>
                        <textarea name="deskripsi_singkat" rows="4" placeholder="Tuliskan bait puisi atau paragraf pembuka..." required><?= $edit_data ? htmlspecialchars($edit_data['deskripsi_singkat']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Gambar Latar Kartu (Opsional)</label>
                        <input type="file" name="poster_karya" accept="image/*">
                        <?php if ($edit_data && !empty($edit_data['poster_karya'])): ?>
                            <small class="help-text">Gambar saat ini: <?= htmlspecialchars($edit_data['poster_karya']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                    <button type="submit" name="<?= $edit_data ? 'edit_karya' : 'tambah_karya'; ?>"
                        class="btn-primary-gold">
                        <?= $edit_data ? 'Simpan Perubahan' : '+ Tambah Karya'; ?>
                    </button>
                    </div>
                </form>
            </section>

            <!-- TABEL DAFTAR KARYA (READ & DELETE) -->
            <section class="admin-card">
                <h3 class="admin-card-title">Daftar Karya Terpublikasi</h3>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Judul Karya</th>
                                <th>Penulis</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>


                        <tbody>
<?php
$no = 1;

foreach ($karya as $row):
?>

<tr>
    <td><?= $no++; ?></td>

    <td>
        <span class="badge-kategori">
            <?= htmlspecialchars($row['kategori_karya']); ?>
        </span>
    </td>

    <td>
        <strong>
            <?= htmlspecialchars($row['judul_karya']); ?>
        </strong>

        <p>
            <?= htmlspecialchars(substr($row['deskripsi_singkat'], 0, 85)); ?>...
        </p>
    </td>

    <td><?= htmlspecialchars($row['penulis']); ?></td>

    <td><?= htmlspecialchars($row['tanggal_publikasi']); ?></td>

    <td>
        <?php if ($row['status_publikasi'] == 'Terbit'): ?>
            <span class="status-terbit">Terbit</span>
        <?php else: ?>
            <span class="status-draft">Draft</span>
        <?php endif; ?>
    </td>

    <td class="aksi-buttons">

        <a href="kelola_karya.php?edit=<?= $row['id_karya']; ?>"
           class="btn-edit">
            Edit
        </a>

        <a href="kelola_karya.php?hapus=<?= $row['id_karya']; ?>"
           class="btn-hapus"
           onclick="return confirm('Yakin ingin menghapus karya ini? Data akan dihapus permanen.')">
            Hapus
        </a>

        <?php if ($row['status_publikasi'] == 'Draft'): ?>

            <a href="kelola_karya.php?publikasi=<?= $row['id_karya']; ?>"
               class="btn-publikasi"
               onclick="return confirm('Yakin ingin memublikasikan karya ini? Karya akan tampil di halaman publik.')">
                Publikasikan
            </a>

        <?php else: ?>

            <span class="status-terbit">Sudah Terbit</span>

        <?php endif; ?>

    </td>
</tr>

<?php endforeach; ?>

<?php if ($total_karya == 0): ?>

<tr>
    <td colspan="7" style="text-align:center; padding:30px;">
        Belum ada data karya.
    </td>
</tr>

<?php endif; ?>

</tbody>



                    </table>
                </div>
            </section>
        </main>
    </div>

</body>
</html>