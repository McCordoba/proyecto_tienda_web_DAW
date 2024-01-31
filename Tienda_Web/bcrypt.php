<?php
if (isset($_POST["clave"])) {

    $clave = $_POST["clave"];

    $opciones = [
        "cost" => 11,
    ];

    echo password_hash($clave, PASSWORD_BCRYPT, $opciones);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obtener hash para bcrypt</title>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
        <input type="submit">
    </form>
</body>

</html>