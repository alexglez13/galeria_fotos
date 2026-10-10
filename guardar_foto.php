<?php
require_once "conexion.php";

header("Content-Type: text/plain; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || empty($_POST["image_data"])) {
    http_response_code(400);
    exit("No se recibieron datos de imagen.");
}

$datos = $_POST["image_data"];

// Admite cadenas Base64 con o sin encabezado Data URL.
if (preg_match('/^data:image\/(jpeg|png|webp);base64,/', $datos, $coincidencia)) {
    $datos = substr($datos, strpos($datos, ",") + 1);
}

$imagen = base64_decode($datos, true);

if ($imagen === false || strlen($imagen) > 10 * 1024 * 1024) {
    http_response_code(400);
    exit("La imagen es inválida o supera los 10 MB.");
}

// Verificar que realmente sea una imagen permitida.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipo = $finfo->buffer($imagen);

$extensiones = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

if (!isset($extensiones[$tipo])) {
    http_response_code(400);
    exit("Formato no permitido.");
}

$carpeta = __DIR__ . "/uploads/";

if (!is_dir($carpeta) &&
    !mkdir($carpeta, 0755, true)) {
    http_response_code(500);
    exit("No se pudo crear la carpeta uploads.");
}

$nombre = bin2hex(random_bytes(16))
        . "." . $extensiones[$tipo];

$ruta = "uploads/" . $nombre;
$destino = $carpeta . $nombre;

if (file_put_contents($destino, $imagen) === false) {
    http_response_code(500);
    exit("Error al guardar la fotografía.");
}

$stmt = $db->prepare(
    "INSERT INTO fotos (url, fecha) VALUES (?, NOW())"
);
$stmt->bind_param("s", $ruta);

if (!$stmt->execute()) {
    unlink($destino);
    http_response_code(500);
    exit("Error al registrar la fotografía.");
}

echo "OK";
?>