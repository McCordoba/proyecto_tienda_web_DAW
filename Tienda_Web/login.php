<?php
require_once "bd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    $usu = comprobar_usuario($usuario, $clave);

    if ($usu == false) {
        $err = true;
        $usuario = $_POST["usuario"];
    } else {
        session_start();
        $_SESSION["usuario"] = $usuario;
        $_SESSION["carrito"] = [];
        header("Location: categorias.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de login</title>
</head>

<body>
    <div class="contenedor">
        <h2>Inicio de sesion</h2>
        <form class="formulario" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" value="<?php if (isset($usuario)) echo $usuario ?>">

            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave">

            <input type="submit">
        </form>

        <?php if (isset($_GET["redirigido"])) {
            echo "<p class='error'>Debes hacer login para continuar</p>";
        } ?>

        <?php if (isset($err) && $err == true) {
            echo "<p class='error'>Revise usuario y contraseña</p>";
        } ?>
    </div>
</body>

</html>