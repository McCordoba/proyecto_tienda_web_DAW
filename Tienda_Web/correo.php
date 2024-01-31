<?php

use PHPMailer\PHPMailer\PHPMailer;

require "vendor/autoload.php";

// Funcion para leer información del servidor de correo (similar a leer_config($nombre, $esquema) del archivo bd)
function leer_config_correo($nombre, $esquema)
{
    // Habilita el manejo de errores internos de libxml
    libxml_use_internal_errors(true);

    // Carga y valida el archivo XML con el esquema
    $configuracion = new DOMDocument;
    $configuracion->load($nombre);

    // Valida el documento XML usando el esquema de configuracion XSD
    if (!$configuracion->schemaValidate($esquema)) {
        $errores = libxml_get_errors();
        foreach ($errores as $error) {
            echo "Error XML: " . $error->message;
        }
        // Si el archivo XML no cumple con el esquema, se lanzará una excepción
        throw new Exception("El archivo de configuración no es válido");
    }

    // Extrae la información de conexión desde el archivo XML
    $host = $configuracion->getElementsByTagName("host")->item(0)->nodeValue;
    $puerto = $configuracion->getElementsByTagName("puerto")->item(0)->nodeValue;

    // Devuelve un array con la info del archivo XML
    return array("host" => $host, "puerto" => $puerto);
}

// Funcion para crear el contenido del correo que se envia despues de hacer un pedido
// Incluye el número de pedido, el restaurante que lo realiza y una tabla HTML con los productos del pedido
function crear_correo($carrito, $pedido, $correo)
{
    $texto = "<h1>Pedido nº $pedido: </h1> 
    <h2>Restaurante: $correo </h2>";
    $texto .= "<style>
    table, td, th {
        border: 1px solid #000;
        border-collapse: collapse;
    }

    th {
        background-color: lightblue;
    }
    </style>";
    $texto .= "Detalle del pedido:";
    $texto .= "<table>";
    $texto .= " <tr>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Peso</th>
                    <th>Unidades</th>
                </tr>";

    $pesoTotal = 0; // Variable para almacenar el peso total del pedido
    // Solo funciona si se llama a la funcion cargar_productos();
    $carrito = cargar_productos(array_keys($_SESSION["carrito"]));
    // Comprobar que el array $carrito, sea un objeto o un array, si no PHP se ralla
    if (is_array($carrito) || is_object($carrito)) {
        foreach ($carrito as $producto) {
            $cod = $producto["codProd"];
            $nom = $producto["nombre"];
            $des = $producto["descripcion"];
            $peso = $producto["peso"];
            $unidades = $_SESSION["carrito"][$cod];

            // Añade el peso del producto al peso total del pedido
            $pesoTotal += $peso * $unidades;

            $texto .= "<tr>
                    <td>$nom</td>
                    <td>$des</td>
                    <td>$peso</td>
                    <td>$unidades</td> </tr>";
        }
    }

    $texto .= "</table>";

    // Añade el peso total al contenido del correo
    $texto .= "<p>Peso total del pedido: $pesoTotal</p>";
    return $texto;
}

// Funcion que recibe un array de direcciones de correo, el cuerpo del correo y opcionalmente el asunto. Envía el correo a todas las direcciones.
function enviar_correos_multiples($lista_correos, $cuerpo, $asunto)
{
    // Llamada a la función para obtener la configuración del servidor de correo
    $configuracionCorreo = leer_config_correo("configuracion_correo.xml", "configuracion_correo.xsd");

    $mail = new PHPMailer();
    // Configuración del servidor de correo
    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = $configuracionCorreo["host"];
    $mail->SMTPAuth = false;
    $mail->Port = $configuracionCorreo["puerto"];

    // Configuración del correo de la Tienda
    $mail->setFrom("correoTienda@gmail.com", "Tienda web");
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body = $cuerpo;

    // Envío de correos a múltiples direcciones
    foreach ($lista_correos as $destinatario) {
        $mail->addAddress($destinatario);
        if ($mail->send()) {
            echo "Correo enviado a $destinatario<br>";
        } else {
            echo "Error al enviar el correo a $destinatario: " . $mail->ErrorInfo . "<br>";
        }
        $mail->clearAddresses();
    }
}

/* Funcion que recibe el carrito de la compra, el número de pedido y el correo del restaurante que lo hace. 
    Primero llama a la función crear_correo() para crear el cuerpo, y luego
    llama enviar_correo_multiples() para enviar el correo. */
function enviar_correos($carrito, $pedido, $correo)
{
    // Llamada a la función para obtener la configuración del servidor de correo
    $configuracionCorreo = leer_config_correo("configuracion_correo.xml", "configuracion_correo.xsd");

    $cuerpo = crear_correo($carrito, $pedido, $correo);
    $asunto = "Confirmacion de pedido nº $pedido";
    $lista_correos = [$correo];

    try {
        // Lamada a la funcion para obtener la cofiguracion del servidor de correo
        enviar_correos_multiples($lista_correos, $cuerpo, $asunto, $configuracionCorreo);
    } catch (Exception $e) {
        // Maneja cualquier excepcion que pueda ocurrir al leer la configuración
        echo "Error: " . $e->getMessage();
    }
}
