<?php
session_start();
require_once "bd.php";

$cod = $_POST["cod"];
$unidades = $_POST["unidades"];

// si existe el codigo restamos las unidades, con minimo de 0
if (isset($_SESSION["carrito"][$cod])) {
    $_SESSION["carrito"][$cod] -= $unidades;

    if ($_SESSION["carrito"][$cod] <= 0) {
        unset($_SESSION["carrito"][$cod]);
    }
}

header("Location: carrito.php");
