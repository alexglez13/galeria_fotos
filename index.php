<?php
require_once "conexion.php";

$resultado = $db->query(
    "SELECT id, url, fecha FROM fotos ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fotografias recibidas</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #eef2f7;
    color: #1e293b;
}

header {
    background: #172554;
    color: white;
    padding: 28px 20px;
    text-align: center;
}

header h1 {
    margin: 0 0 10px;
}

main {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.estado {
    background: white;
    padding: 16px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.galeria {
    display: grid;
    grid-template-columns: repeat(
        auto-fill, minmax(240px, 1fr)
    );
    gap: 20px;
}

.tarjeta {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px #00000012;
}

.tarjeta img {
    width: 100%;
    height: 240px;
    object-fit: contain;
    background: #dbe3ee;
    display: block;
    cursor: pointer;
}

.info {
    padding: 15px;
}

.info h3 {
    margin: 0 0 8px;
}

.info p {
    font-size: 13px;
    color: #64748b;
}

.info a {
    color: #2563eb;
    text-decoration: none;
}

.vacio {
    background: white;
    padding: 35px;
    border-radius: 12px;
    text-align: center;
}
</style>
</head>

<body>
<header>
    <h1>📸 Fotografías recibidas</h1>
    <p>Imágenes enviadas desde MIT App Inventor</p>
</header>

<main>
    <div class="estado">
        <?php
        echo "Total de fotografias: " .
            ($resultado ? $resultado->num_rows : 0);
        ?>
        <button onclick="location.reload()"
                style="float:right;cursor:pointer">
            Actualizar galería
        </button>
    </div>

    <section class="galeria">
    <?php if ($resultado && $resultado->num_rows > 0): ?>

        <?php while ($foto = $resultado->fetch_assoc()): ?>
            <article class="tarjeta">
                <a href="<?php
                    echo htmlspecialchars($foto["url"], ENT_QUOTES, "UTF-8");
                ?>" target="_blank" rel="noopener">
                    <img
                        src="<?php
                            echo htmlspecialchars($foto["url"], ENT_QUOTES, "UTF-8");
                        ?>"
                        alt="Fotografia recibida"
                        loading="lazy">
                </a>

                <div class="info">
                    <h3>Fotografía #<?php
                        echo (int)$foto["id"];
                    ?></h3>

                    <p>Fecha: <?php
                        echo htmlspecialchars($foto["fecha"], ENT_QUOTES, "UTF-8");
                    ?></p>

                    <a href="<?php
                        echo htmlspecialchars($foto["url"], ENT_QUOTES, "UTF-8");
                    ?>" target="_blank" rel="noopener">
                        Ver fotografía completa
                    </a>
                </div>
            </article>
        <?php endwhile; ?>

    <?php else: ?>
        <div class="vacio">
            <h2>No hay fotografías todavía</h2>
            <p>Toma una fotografía desde la aplicación y envíala al servidor.</p>
        </div>
    <?php endif; ?>
    </section>
</main>
</body>
</html>