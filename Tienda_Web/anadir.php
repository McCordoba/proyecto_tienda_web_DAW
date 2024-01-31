<?php
session_start();
require_once "bd.php";

$cod = $_POST["cod"];
$unidades = (int)$_POST["unidades"];
$categoria = $_POST["categoria"];

// si existe el codigo le sumamos las unidades
if (isset($_SESSION["carrito"][$cod])) {
    $_SESSION["carrito"][$cod] += $unidades;
} else {
    $_SESSION["carrito"][$cod] = $unidades;
}

// Redirige a los productos con la misma categoria
header("Location: productos.php?categoria=" . $categoria);
