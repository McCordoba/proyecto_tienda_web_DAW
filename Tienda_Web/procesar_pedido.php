<?php
session_start();
require "correo.php";
require_once "bd.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
</head>

<body>
    <?php include "cabecera.php"; ?>
    <div class="contenedor">
        <?php
        // Realiza una consulta SQL para obtener el valor de 'codRes' desde la base de datos
        $codRes = obtener_cod_res($_SESSION["usuario"]);
        // $productos = cargar_productos(array_keys($_SESSION["carrito"]));

        // Declaro la sesion carrito
        $compra = $_SESSION["carrito"];

        if ($codRes === false) {
            echo "<p class='error'>No se ha podido obtener 'codRes' desde la base de datos</p>";
        } else {
            if (empty($compra)) {
                echo "<p class='error'>No se ha podido realizar el pedido, el carrito está vacío</p>";
            } else {
                $pedido = insertar_pedido($_SESSION["carrito"], $codRes);
                $correo = $_SESSION["usuario"];
                echo "<p class='exito'>Pedido realizado con éxito. Se enviará un correo de información a: $correo</p>";
                enviar_correos($compra, $pedido, $correo);

                // Cuando se envia el mail, se vacia el carrito
                $_SESSION["carrito"] = [];
            }
        }
        ?>
    </div>
</body>

</html>