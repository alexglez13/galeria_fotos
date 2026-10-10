<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "galeria_fotos";

$db = new mysqli($host, $usuario, $password, $base_datos);

if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
}

$db->set_charset("utf8mb4");
?>