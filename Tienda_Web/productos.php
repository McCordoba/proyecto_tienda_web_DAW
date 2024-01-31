<?php
session_start();
require_once "bd.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de productos por categoria</title>
</head>

<body>
    <?php include "cabecera.php"; ?>

    <div class="contenedor">
        <h2>Productos por categoria</h2>
        <?php
        $categoria = $_GET["categoria"];
        $cat = cargar_categoria($_GET["categoria"]);
        $productos = cargar_productos_categorias($_GET["categoria"]);
        if ($cat === false || $productos === false) {
            echo "<p class='error'> Error al conectar con la base de datos</p>";
            exit;
        }

        echo "<h3>" . $cat["nombre"] . "</h3>";
        echo "<p>" . $cat["descripcion"] . "</p>";

        echo "<table class='tabla'>";
        echo "<tr>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Peso</th>
            <th>Stock</th>
            <th>Comprar</th>
        </tr>";

        foreach ($productos as $producto) {
            $cod = $producto["codProd"];
            $nom = $producto["nombre"];
            $des = $producto["descripcion"];
            $peso = $producto["peso"];
            $stock = $producto["stock"];

            // Verifica si el producto tiene stock antes de mostrarlo
            if ($stock > 0) {
                echo "<tr>
                    <td>$nom</td>
                    <td>$des</td>
                    <td>$peso</td>
                    <td>$stock</td>
                    <td>
                        <form action='anadir.php' method='POST'>
                            <input type='number' name='unidades' id='unidades' min='1' value='1'>
                            <input type='submit' value='comprar'>
                            <input type='hidden' name='cod' value='$cod'>
                            <input type='hidden' name='categoria' value='$categoria'>
                        </form>
                    </td>
                </tr>";
            }
        }
        echo "</table>";
        ?>
    </div>
</body>

</html>