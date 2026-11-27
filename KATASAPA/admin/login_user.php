<?php
session_start();

if (isset($_POST['clogin'])) {
    $tuser = isset($_POST['tuser']) && is_string($_POST['tuser']) ? trim($_POST['tuser']) : '';
    $tpass = isset($_POST['tpass']) && is_string($_POST['tpass']) ? $_POST['tpass'] : '';

    require_once __DIR__ . '/../config/database.php';
    $db = new Database();
    $user = $tuser !== '' ? $db->get('users', ['username' => $tuser]) : null;
    $validPassword = false;
    $legacyPassword = false;

    if ($user && isset($user['password'])) {
        $storedPassword = (string) $user['password'];
        $validPassword = password_verify($tpass, $storedPassword);
        $legacyPassword = !$validPassword
            && strlen($storedPassword) === 32
            && hash_equals($storedPassword, md5($tpass));
        $validPassword = $validPassword || $legacyPassword;
    }

    if ($validPassword && ($user['role'] ?? '') === 'admin') {
        if ($legacyPassword || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            $db->execute(
                'UPDATE users SET password = ? WHERE id_user = ?',
                [password_hash($tpass, PASSWORD_DEFAULT), $user['id_user']]
            );
        }
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: dashboard_admin.php');
        exit;
    }

    $loginError = $user && ($user['role'] ?? '') !== 'admin'
        ? 'Akun tidak memiliki akses admin!'
        : 'Username atau password salah!';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | KATASAPA</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- CSS Login -->
    <link rel="stylesheet" href="../assets/css/login_admin.css">

</head>

<body>

    <main class="login-container">

        <!-- BAGIAN KIRI -->
        <section class="login-left">

            <div class="brand">
                <h1>KATASAPA</h1>
                <p>RUANG KARYA DAN SASTRA</p>
            </div>

            <div class="welcome-content">

                <div class="subtitle">
                    Welcome to Katasapa
                </div>

                <h2>
                    Setiap Kata<br>
                    Memiliki <span>Cerita.</span>
                </h2>

                <p>
                    Sebuah ruang untuk mengenal, mengapresiasi,
                    dan merayakan karya tulis dari berbagai penulis.
                    Karena setiap tulisan memiliki cerita yang layak dibaca.
                </p>

            </div>

            <div class="literature-quote">
                <p>
                    "Menulis adalah cara mengabadikan
                    pikiran agar tetap hidup dalam waktu."
                </p>

                <span>— KATASAPA</span>
            </div>

            <div class="left-footer">
                &copy; 2026 KATASAPA. All Rights Reserved.
            </div>

        </section>


        <!-- BAGIAN KANAN -->
        <section class="login-right">

            <div class="login-box">

                <a href="../index.php" class="back-home">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>

                <div class="login-heading">

                    <div class="small-title">
                        Katasapa Administration
                    </div>

                    <h2>Selamat Datang.</h2>

                    <p>
                        Silakan masuk menggunakan akun yang telah
                        terdaftar untuk mengelola KATASAPA.
                    </p>

                </div>


                <!-- FORM LOGIN -->
                <?php if (isset($loginError)): ?>
                    <p class="login-error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <form action="login_user.php" method="post">

                    <div class="form-group">

                        <label for="loginEmail">
                            Username
                        </label>

                        <div class="input-wrapper">

                            <i class="bi bi-person"></i>

                            <input
                                id="loginEmail"
                                type="text"
                                name="tuser"
                                placeholder="Masukkan username"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="loginPassword">
                            Password
                        </label>

                        <div class="input-wrapper">

                            <i class="bi bi-lock"></i>

                            <input
                                id="loginPassword"
                                type="password"
                                name="tpass"
                                placeholder="Masukkan password"
                                required
                            >

                        </div>

                    </div>


                    <button
                        type="submit"
                        name="clogin"
                        value="1"
                        class="btn-login"
                    >
                        MASUK SEKARANG
                        <i class="bi bi-arrow-right"></i>
                    </button>

                </form>


                <div class="security-info">
                    <i class="bi bi-shield-lock"></i>
                    Halaman login khusus pengelola KATASAPA
                </div>


                <div class="right-footer">
                    KATASAPA &bull; Ruang Apresiasi Karya
                </div>

            </div>

        </section>

    </main>

</body>
</html>