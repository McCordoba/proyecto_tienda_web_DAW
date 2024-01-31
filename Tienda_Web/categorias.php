<?php
session_start();
require_once "bd.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
</head>

<body>
    <?php include "cabecera.php"; ?>
    <div class="contenedor">
        <h1>Lista de categorias</h1>

        <?php
        $categorias = cargar_categorias();

        if ($categorias === false) {
            echo "<p class='error'>Error al conectar con la base de datos</p>";
        } else {
            echo "<ul class='lista'>"; // abrir la lista
            foreach ($categorias as $cat) {
                $url = "productos.php?categoria=" . $cat["codCat"];
                echo "<li>
                        <a href='$url'>" . $cat["nombre"] . "</a>
                      </li>";
            }
            echo "</ul>"; // cerrar la lista
        }
        ?>
    </div>
</body>

</html>