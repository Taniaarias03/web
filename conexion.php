<?php
$host = "localhost";
$dbname = "vida_y_salud";
$username = "root";
$password = ""; // Cambia si tu servidor tiene contraseña

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
