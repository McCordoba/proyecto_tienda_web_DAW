<?php
// Función para leer la configuración desde un archivo XML y validarla con un esquema XSD
function leer_config($nombre, $esquema)
{
    // Habilitar el manejo de errores internos de libxml
    libxml_use_internal_errors(true);

    // Cargar y validar el archivo XML con el esquema
    $configuracion = new DOMDocument;
    $configuracion->load($nombre);

    if (!$configuracion->schemaValidate($esquema)) {
        $errores = libxml_get_errors();
        foreach ($errores as $error) {
            echo "Error XML: " . $error->message;
        }

        // Si el archivo XML no cumple con el esquema, se lanzará una excepción
        throw new Exception("El archivo de configuración no es válido");
    }

    // Extraer la información de conexión desde el archivo XML
    $servidor = $configuracion->getElementsByTagName("servidor")->item(0)->nodeValue;
    $nombre = $configuracion->getElementsByTagName("nombre")->item(0)->nodeValue;
    $usuario = $configuracion->getElementsByTagName("usuario")->item(0)->nodeValue;
    $clave = $configuracion->getElementsByTagName("clave")->item(0)->nodeValue;
    $puerto = $configuracion->getElementsByTagName("puerto")->item(0)->nodeValue;

    // Construir la cadena para la conexión con la BD
    $cadenaConexion = "mysql:host=$servidor;port=$puerto;dbname=$nombre";

    return array(
        "cadenaConexion" => $cadenaConexion,
        "usuario" => $usuario,
        "clave" => $clave,
        "puerto" => $puerto
    );
}

// Función para obtener la conexión a la base de datos (No lo pide pero asi es mas comodo)
function obtener_conexion()
{
    $configuracion = leer_config("configuracion.xml", "configuracion.xsd");
    $cadenaConexion = $configuracion["cadenaConexion"];
    $usuario = $configuracion["usuario"];
    $clave = $configuracion["clave"];

    return new PDO($cadenaConexion, $usuario, $clave);
}

try {
    // Lamada a la funcion para obtener la conexión a la base de datos 
    obtener_conexion();
} catch (Exception $e) {
    // Manejar cualquier excepción que pueda ocurrir al leer la configuración
    echo "Error: " . $e->getMessage();
}

// Función para comprobar el usuario y su clave en la BD
function comprobar_usuario($usuario, $clave)
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    // Consulta para verificar el usuario y contraseña
    $sql = "SELECT * FROM Restaurantes WHERE correo = :usuario";
    $preparada = $conexion->prepare($sql);
    $preparada->bindParam(":usuario", $usuario);
    $preparada->execute();

    if ($preparada->rowCount() == 1) {
        $fila = $preparada->fetch();
        $claveAlmacenada = $fila["clave"];

        // Compara la contraseña proporcionada con la almacenada usando password_verify
        if (password_verify($clave, $claveAlmacenada)) {
            // Las contraseñas coinciden, usuario identificado
            return true;
        }
    }

    // El usuario no está identificado
    return false;
}

// Devuelve un cursor con las categorias
function cargar_categorias()
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    // Consulta SQL para obtener todas las categorías
    $sql = "SELECT codCat, nombre, descripcion FROM Categorias";

    // Preparar la consulta
    $preparada = $conexion->prepare($sql);
    $preparada->execute();

    // Obtener el resultado
    $categorias = $preparada->fetchAll();

    if (!$categorias) {
        return false;
    }

    return $categorias;
}

// Devuelve los datos de la categoria
function cargar_categoria($codCat)
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    // Consulta SQL para obtener la categoría
    $sql = "SELECT nombre, descripcion FROM Categorias WHERE codCat = :codCat";

    // Preparar la consulta
    $preparada = $conexion->prepare($sql);
    $preparada->bindParam(':codCat', $codCat);
    $preparada->execute();

    // Obtener el resultado
    $categoria = $preparada->fetch();

    if (!$categoria) {
        return false;
    }

    return $categoria;
}

// Devuelve un cursor con los productos de la categoria
function cargar_productos_categorias($codCat)
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    // Consulta SQL para obtener la categoría
    $sql = "SELECT * FROM Productos WHERE codCat = :codCat";

    // Preparar la consulta
    $preparada = $conexion->prepare($sql);
    $preparada->bindParam(":codCat", $codCat);
    $preparada->execute();

    // Obtener el resultado
    $productosCategoria = $preparada->fetchAll();

    if (!$productosCategoria) {
        return false;
    }

    return $productosCategoria;
}

// Función para obtener el valor de 'codRes' a partir del correo del usuario
function obtener_cod_res($correo)
{
    $conexion = obtener_conexion();

    // Consulta SQL para obtener 'codRes' a partir del correo
    $sql = "SELECT codRest FROM Restaurantes WHERE correo = :correo";

    $preparada = $conexion->prepare($sql);
    $preparada->bindParam(":correo", $correo);
    $preparada->execute();

    if ($preparada->rowCount() == 1) {
        $resultado = $preparada->fetch();
        return $resultado["codRest"];
    } else {
        return false; // No se encontró el valor de 'codRes'
    }
}

// Devuelve un cursor de productos a partir de sus codigos
function cargar_productos($codigosProd)
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    // Si el carrito está vacío, no se ejecuta la consulta
    if (empty($codigosProd)) {
        return false;
    }

    // implode() une elementos de un array en un string
    $carrito = implode(",", $codigosProd);

    // Consulta SQL para obtener los productos
    $sql = "SELECT * FROM Productos WHERE codProd IN ($carrito)";

    // Preparar la consulta
    $preparada = $conexion->prepare($sql);
    $preparada->execute();

    // Obtener el resultado
    $codigosProd = $preparada->fetchAll();

    // Si la consulta no encontra productos con los códigos proporcionados
    if (!$codigosProd) {
        return false;
    }

    return $codigosProd;
}

// Inserta el pedido en la BD
function insertar_pedido($carrito, $codRes)
{
    // Conexión a la base de datos
    $conexion = obtener_conexion();

    try {
        $conexion->beginTransaction();

        $hora = date("Y-m-d H:i:s", time());

        // Inserta el pedido 
        $sql = "INSERT INTO Pedidos (fecha, enviado, codRest) VALUES (:hora, 0, :codRes)";
        $preparada = $conexion->prepare($sql);
        // Se utiliza PDO::PARAM_STR para que no haya errores de tipo de dato al insertar la consulta en la BD
        $preparada->bindParam(":hora", $hora, PDO::PARAM_STR);
        $preparada->bindParam(":codRes", $codRes, PDO::PARAM_INT);
        $preparada->execute();

        // Obtener el ID del nuevo pedido
        $pedido = $conexion->lastInsertId();

        // Inserta las filas en PedidosProductos y actualizar Productos
        foreach ($carrito as $codProd => $unidades) {
            $sql = "INSERT INTO PedidosProductos (codPed, codProd, unidades) VALUES (:pedido, :codProd, :unidades)";
            $preparada = $conexion->prepare($sql);
            $preparada->bindParam(":pedido", $pedido, PDO::PARAM_INT);
            $preparada->bindParam(":codProd", $codProd, PDO::PARAM_INT);
            $preparada->bindParam(":unidades", $unidades, PDO::PARAM_INT);
            $preparada->execute();

            // Resta las unidades
            $sql = "UPDATE Productos SET stock = stock - :unidades WHERE codProd = :codProd";
            $preparada = $conexion->prepare($sql);
            $preparada->bindParam(":unidades", $unidades, PDO::PARAM_INT);
            $preparada->bindParam(":codProd", $codProd, PDO::PARAM_INT);
            $preparada->execute();
        }

        // Relazia el commit despues de que todas las operaciones hayan realizado con exito
        $conexion->commit();
        return $pedido;
    } catch (PDOException $e) {
        $conexion->rollBack();
        echo "Se produjo un error al insertar los datos: " . $e->getMessage();
        return false;
    }
}
