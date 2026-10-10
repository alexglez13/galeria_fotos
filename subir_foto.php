<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

if (!isset($_FILES["foto"]) ||
    $_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
    exit("Error al seleccionar la fotografía.");
}

$archivo = $_FILES["foto"];

if ($archivo["size"] > 10 * 1024 * 1024) {
    exit("La fotografía supera el límite de 10 MB.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipo = $finfo->file($archivo["tmp_name"]);

$extensiones = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp"
];

if (!isset($extensiones[$tipo]) ||
    getimagesize($archivo["tmp_name"]) === false) {
    exit("Selecciona una imagen JPG, PNG o WebP válida.");
}

$carpeta = __DIR__ . "/uploads/";

if (!is_dir($carpeta) &&
    !mkdir($carpeta, 0755, true)) {
    exit("No se pudo crear la carpeta de imágenes.");
}

$nombre = bin2hex(random_bytes(16))
        . "." . $extensiones[$tipo];

$ruta = "uploads/" . $nombre;

if (!move_uploaded_file(
    $archivo["tmp_name"],
    __DIR__ . "/" . $ruta
)) {
    exit("No se pudo guardar la fotografía.");
}

$stmt = $db->prepare(
    "INSERT INTO fotos (url, fecha) VALUES (?, NOW())"
);
$stmt->bind_param("s", $ruta);

if (!$stmt->execute()) {
    unlink(__DIR__ . "/" . $ruta);
    exit("No se pudo registrar la fotografía.");
}

header("Location: index.php?estado=guardado");
exit;
?>