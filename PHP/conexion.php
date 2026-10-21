<?php
// Conexión a MySQL. Los datos salen de las variables de entorno (.env).
date_default_timezone_set("America/Guatemala");
mysqli_report(MYSQLI_REPORT_OFF);

$host = getenv("DB_HOST") ?: "";
$user = getenv("DB_USERNAME") ?: "";
$pass = getenv("DB_PASSWORD") ?: "";
$db   = getenv("DB_DATABASE") ?: "";
$port = (int)(getenv("DB_PORT") ?: 3306);

$cn = mysqli_connect($host, $user, $pass, $db, $port);

// Si algo falla, se corta con un error en JSON y sin mostrar credenciales.
if (!$cn) {
    exit('{"error":"No se pudo conectar a la base de datos"}');
}

mysqli_set_charset($cn, "utf8mb4");
mysqli_query($cn, "SET time_zone = '-06:00'");
