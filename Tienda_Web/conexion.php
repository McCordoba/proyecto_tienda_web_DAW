<?php

// No se usa, solo para pruebas 
// La conexon esta en el archivo XML configuracion
/*
$servidor = "localhost";
$usuario = "usuario1";
$password = "1234";

  $conexion = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $password);
*/

/*
$servidor = "localhost";
$usuario = "root";
$password = "";
$db = "empresa";
$port = "3307";

$conexion = new PDO("mysql:host=$servidor;port=$port;dbname=$db", $usuario, $password);
*/
$servidor = "localhost";
$usuario = "root";
$password = "";
$db = "empresa";
$port = "3307";


try {
    // Crear conexion
    $conexion = new PDO("mysql:host=$servidor;port=$port;dbname=$db", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexion realizada con exito <br>";
} catch (PDOException $e) {
    echo "La conexion ha fallado: " . $e->getMessage();
}
