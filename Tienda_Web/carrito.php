<?php
session_start();
require_once "bd.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
</head>

<body>
    <?php include "cabecera.php"; ?>
    <div class="contenedor">
        <h2>Carrito de la compra</h2>

        <?php
        $productos = cargar_productos(array_keys($_SESSION["carrito"]));

        // Verifica si $productos es un array antes de intentar iterar
        // Si no se hace esto da errores si el carrito esta vacio
        if (is_array($productos)) {
            echo "<table class='tabla'>";
            echo "<tr>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Peso</th>
            <th>Unidades</th>
            <th>Eliminar</th>
            </tr>";

            foreach ($productos as $producto) {
                $cod = $producto["codProd"];
                $nom = $producto["nombre"];
                $des = $producto["descripcion"];
                $peso = $producto["peso"];
                $unidades = $_SESSION["carrito"][$cod];

                echo "<tr>
                <td>$nom</td>
                <td>$des</td>
                <td>$peso</td>
                <td>$unidades</td>
                <td>
                    <form class='formulario' action='eliminar.php' method='POST'>
                        <input type='number' name='unidades' id='unidades' min='1' value='1'>
                        <input type='submit' value='Eliminar'> 
                        <input type='hidden' name='cod' value='$cod'>
                    </form>
                </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay productos en el pedido</p>";
        }
        ?>

        <a href="procesar_pedido.php">Realizar pedido</a>
    </div>
</body>

</html>