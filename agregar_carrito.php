<?php
session_start();
include("conexion.php");

// Verificar si el cliente está autenticado
if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente']) || $_SESSION['tipo'] != 3) {
    echo "<script>
        alert('⚠️ Debes iniciar sesión como cliente para agregar productos al carrito.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// Depuración para verificar la variable de sesión
// var_dump($_SESSION);  // Descomenta esta línea para depurar la sesión

$idcliente = $_SESSION['id_cliente'];  // Asegúrate de que estás usando 'id_cliente' correctamente
$idproducto = $_POST['idproducto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;
$tipo_venta = $_POST['tipo_venta'] ?? 'menudeo';
$unidad_medida = $_POST['unidad_medida'] ?? 'pieza';

// Validar datos mínimos
if (!$idproducto || $cantidad < 1) {
    echo "<script>
        alert('❌ Datos inválidos para agregar al carrito.');
        window.location.href = 'tienda.php';
    </script>";
    exit();
}

// Obtener precios según tipo de venta
$sql = "SELECT precio_mayoreo, precio_menudeo FROM productos WHERE idproductos = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idproducto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<script>
        alert('❌ Producto no encontrado.');
        window.location.href = 'tienda.php';
    </script>";
    exit();
}

$producto = $resultado->fetch_assoc();
$precio = $tipo_venta === 'mayoreo' ? $producto['precio_mayoreo'] : $producto['precio_menudeo'];

// Buscar o crear carrito activo
$sql = "SELECT idcarrito FROM carrito WHERE clientes_idclientes = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idcliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Si ya hay un carrito activo, obtener su ID
    $idcarrito = $res->fetch_assoc()['idcarrito'];
} else {
    // Si no hay un carrito activo, crear uno nuevo
    $estado = 'activo';
    $sql_insert = "INSERT INTO carrito (estado, clientes_idclientes) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("si", $estado, $idcliente);
    $stmt->execute();
    $idcarrito = $conn->insert_id;  // Obtener el ID del carrito recién insertado
}

// Verificar si el producto ya está en el carrito
$sql_check = "SELECT cantidad FROM carrito_has_productos WHERE carrito_idcarrito = ? AND productos_idproductos = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $idcarrito, $idproducto);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Si el producto ya está en el carrito, actualizar la cantidad
    $row = $res->fetch_assoc();
    $nueva_cantidad = $row['cantidad'] + $cantidad;

    $sql_update = "UPDATE carrito_has_productos 
                   SET cantidad = ?, tipo_venta = ?, unidad_medida = ? 
                   WHERE carrito_idcarrito = ? AND productos_idproductos = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("issii", $nueva_cantidad, $tipo_venta, $unidad_medida, $idcarrito, $idproducto);
    $stmt->execute();
} else {
    // Si el producto no está en el carrito, insertarlo con los detalles
    $sql_insert = "INSERT INTO carrito_has_productos 
                   (carrito_idcarrito, carrito_clientes_idclientes, productos_idproductos, cantidad, tipo_venta, unidad_medida)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("iiiiss", $idcarrito, $idcliente, $idproducto, $cantidad, $tipo_venta, $unidad_medida);
    $stmt->execute();
}

// Confirmación
echo "<script>
    alert('✅ Producto agregado al carrito por $tipo_venta. Precio unitario: $" . number_format($precio, 2) . "');
    window.location.href = 'carrito.php';
</script>";
exit();
?>
