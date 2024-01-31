<?php
// Destruye la sesión
session_destroy();
// Redirige al usuario a la página de inicio de sesión
header("Location: login.php");
exit();
