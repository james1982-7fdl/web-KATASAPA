<?php 
require_once 'config/database.php'; 

?>




<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - KATASAPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/tentang.css">
    
</head>
<body class="halaman-tentang">

    <header class="navbar">
        <div class="logo-container">
            <img src="assets/img/instagram/katasapa-profile.jpg" alt="Logo Katasapa" class="logo-img">
            <div>
                <h1 class="logo-text">KATASAPA</h1>
                <p class="logo-subtext">Komunitas Teater dan Sastra Perwira</p>
            </div>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php" class="active">Tentang Kami</a></li>
                <li><a href="kegiatan.php">Kegiatan</a></li>
                <li><a href="karya.php">Karya</a></li>
                <li><a href="dokumentasi.php">Dokumentasi</a></li>
            </ul>
        </nav>
            <a href="admin/login_user.php" class="btn-login">Login</a>
    </header>

    <section class="page-banner">
        <h2>Tentang KATASAPA</h2>
        <p>Sejak 2016, kami hadir sebagai ruang bagi mereka yang percaya bahwa seni dan sastra adalah cara untuk memahami hidup dan merawat kemanusiaan.</p>
    </section>

    <section class="sejarah-grid">
        <img src="https://github.com/faiqohnur11/katasapa/blob/main/katasapa/assets/img/profil.jpg?raw=true" alt="Kegiatan workshop teater KATASAPA" loading="lazy">
        <div class="sejarah-text">
            <h3>Sejarah</h3>
            <p>KATASAPA (Komunitas Teater dan Sastra Perwira) berdiri pada tahun 2016 di Purbalingga, berawal dari sekelompok pemuda yang memiliki kegemaran berteater dan menulis.</p>
            <p>Berangkat dari ruang-ruang diskusi sederhana, KATASAPA kini menjadi ruang berkarya, belajar, dan berbagi bagi siapa saja yang mencintai teater, puisi, prosa, dan kebudayaan lokal di Purbalingga.</p>
        </div>
    </section>

    <section class="vmt-grid">
        <div class="vmt-card">
            <h4>Visi</h4>
            <p>Menjadi komunitas yang berkontribusi dalam pengembangan seni, sastra, dan literasi di masyarakat Purbalingga dan sekitarnya.</p>
        </div>
        <div class="vmt-card">
            <h4>Misi</h4>
            <ol>
                <li>Menyelenggarakan kegiatan seni dan sastra yang edukatif dan inklusif.</li>
                <li>Membina dan memfasilitasi anggota dalam berkarya serta berproses kreatif.</li>
                <li>Memperluas jejaring dan kolaborasi lintas komunitas maupun institusi.</li>
            </ol>
        </div>
        <div class="vmt-card">
            <h4>Kontak</h4>
            <div class="kontak-item">
                <strong>Instagram</strong>
                <span>@katasapa.pbg</span>
            </div>
            <div class="kontak-item">
                <strong>Email</strong>
                <span>katasapapbg@gmail.com</span>
            </div>
            <div class="kontak-item">
                <strong>Lokasi</strong>
                <span>Purbalingga, Jawa Tengah</span>
            </div>
        </div>
    </section>

    <section class="quote-full">
        <div class="quote-card">
            <p>"Seni dan sastra bukan hanya tentang keindahan, tetapi juga tentang keberanian untuk menyuarakan yang tak terlihat."</p>
            <span>— KATASAPA</span>
        </div>
    </section>

    <footer>
        <p><strong>KATASAPA</strong> &copy; 2026 KATASAPA. Semua Hak Dilindungi.</p>
        <p>Instagram: <a href="https://www.instagram.com/katasapa.pbg/" target="_blank" rel="noopener noreferrer">@katasapa.pbg</a></p>
    </footer>

</body>
</html>