<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'organiza_plus';

$conn = null;
$attempts = 0;

while ($attempts < 10) {
    $conn = @mysqli_connect($host, $user, $pass, $name);
    if ($conn) break;
    $attempts++;
    sleep(2);
}

if (!$conn) {
    die('Erro na conexão com o banco de dados.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>