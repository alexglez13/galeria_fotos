<?php
require_once "conexion.php";

header("Content-Type: text/plain; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Utiliza el metodo POST");
}

// Recibir el campo enviado desde App Inventor.
$base64 = $_POST["image_data"] ?? "";

if (empty($base64)) {
    http_response_code(400);
    exit("No se recibio image_data");
}

// Eliminar el encabezado Data URL si existe.
if (strpos($base64, "base64,") !== false) {
    $base64 = substr($base64, strpos($base64, "base64,") + 7);
}

// Decodificar la fotografia.
$imagen = base64_decode($base64, true);

if ($imagen === false || strlen($imagen) > 10 * 1024 * 1024) {
    http_response_code(400);
    exit("Imagen Base64 invalida o demasiado grande");
}

// Verificar que sea una imagen real.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipo = $finfo->buffer($imagen);

$formatos = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

if (!isset($formatos[$tipo])) {
    http_response_code(400);
    exit("El contenido recibido no es una imagen compatible");
}

// Crear la carpeta si no existe.
$carpeta = __DIR__ . "/uploads/";

if (!is_dir($carpeta) &&
    !mkdir($carpeta, 0755, true)) {
    http_response_code(500);
    exit("No se pudo crear uploads");
}

// Nombre unico para evitar sobrescribir fotos.
$nombre = bin2hex(random_bytes(16))
        . "." . $formatos[$tipo];

$ruta = "uploads/" . $nombre;

if (file_put_contents($carpeta . $nombre, $imagen) === false) {
    http_response_code(500);
    exit("No se pudo guardar la imagen");
}

// Guardar la ruta en MySQL.
$stmt = $db->prepare(
    "INSERT INTO fotos (url, fecha) VALUES (?, NOW())"
);

$stmt->bind_param("s", $ruta);

if (!$stmt->execute()) {
    unlink($carpeta . $nombre);
    http_response_code(500);
    exit("Error al registrar la foto en MySQL");
}

echo "OK";
?>