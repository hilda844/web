<?php
$host = "localhost";
$usuario = "root";
$contrasena = "12345";
$base_datos = "proyecto_final";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
