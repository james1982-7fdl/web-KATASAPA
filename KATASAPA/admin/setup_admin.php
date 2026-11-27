Admin username: buat_username_anda
Admin password (minimum 12 characters): ketik_password_pilihan_anda
Confirm password: ketik_ulang_password_yang_sama<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/database.php';
$db = new Database();
$userCount = (int) $db->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

if ($userCount > 0) {
    fwrite(STDERR, "An account already exists; initial admin setup is disabled.\n");
    exit(1);
}

fwrite(STDOUT, "Admin username: ");
$username = trim((string) fgets(STDIN));
fwrite(STDOUT, "Admin password (minimum 12 characters): ");
$password = rtrim((string) fgets(STDIN), "\r\n");
fwrite(STDOUT, "Confirm password: ");
$confirmation = rtrim((string) fgets(STDIN), "\r\n");

if ($username === '' || strlen($username) > 100 || strlen($password) < 12 || $password !== $confirmation) {
    fwrite(STDERR, "Invalid username, password length, or confirmation.\n");
    exit(1);
}

$statement = $db->pdo->prepare(
    'INSERT INTO users (username, password, role) VALUES (:username, :password, :role)'
);
$statement->execute([
    'username' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'role' => 'admin',
]);

fwrite(STDOUT, "Admin account created. You may now sign in.\n");
