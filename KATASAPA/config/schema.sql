CREATE TABLE IF NOT EXISTS kegiatan2 (
    id_kegiatan INT UNSIGNED NOT NULL AUTO_INCREMENT,
    judul_kegiatan VARCHAR(200) NOT NULL,
    kategori_kegiatan VARCHAR(60) NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    waktu_kegiatan TIME NULL,
    waktu_selesai TIME NULL,
    lokasi_kegiatan VARCHAR(255) NOT NULL,
    deskripsi_singkat TEXT NOT NULL,
    deskripsi TEXT NULL,
    poster VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (id_kegiatan),
    KEY idx_kegiatan_tanggal (tanggal_kegiatan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS karya (
    id_karya INT UNSIGNED NOT NULL AUTO_INCREMENT,
    judul_karya VARCHAR(200) NOT NULL,
    penulis VARCHAR(160) NOT NULL,
    kategori_karya VARCHAR(60) NOT NULL,
    deskripsi TEXT NOT NULL,
    file_karya TEXT NULL,
    tanggal_publikasi DATE NOT NULL,
    deskripsi_singkat TEXT NOT NULL,
    poster_karya VARCHAR(255) NOT NULL DEFAULT '',
    status_publikasi VARCHAR(20) NOT NULL DEFAULT 'Draft',
    PRIMARY KEY (id_karya),
    KEY idx_karya_tanggal (tanggal_publikasi),
    KEY idx_karya_status (status_publikasi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dokumentasi (
    id_dokumentasi INT UNSIGNED NOT NULL AUTO_INCREMENT,
    judul_dokumentasi VARCHAR(200) NOT NULL,
    kategori_dokumentasi VARCHAR(20) NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    file_media VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (id_dokumentasi),
    KEY idx_dokumentasi_tanggal (tanggal_kegiatan),
    KEY idx_dokumentasi_kategori (kategori_dokumentasi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id_user INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'admin',
    PRIMARY KEY (id_user),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
