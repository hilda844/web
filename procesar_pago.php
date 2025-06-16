<?php
session_start();
include("conexion.php");

// Validar sesión
if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente']) || $_SESSION['tipo'] != 3) {
    echo "Debes iniciar sesión para proceder con el pago.";
    exit();
}

$idcliente = $_SESSION['id_cliente'];  // Usar 'id_cliente' correctamente
$metodo_pago = $_POST['metodo_pago'] ?? '';
$pagina_origen = $_SESSION['pagina_origen'] ?? 'tienda.php';  // Redirigir a la página original

// 🔍 1. Obtener carrito ACTIVO del cliente
$sql = "SELECT idcarrito FROM carrito WHERE clientes_idclientes = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idcliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No tienes productos en el carrito para procesar.";
    exit();
}

$idcarrito = $res->fetch_assoc()['idcarrito'];

// 🔍 2. Obtener el total del carrito
$sql_total = "SELECT SUM(
                    CASE 
                        WHEN chp.cantidad >= 10 THEN p.precio_mayoreo
                        ELSE p.precio_menudeo
                    END * chp.cantidad
                ) AS total
              FROM carrito_has_productos chp
              JOIN productos p ON chp.productos_idproductos = p.idproductos
              WHERE chp.carrito_idcarrito = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $idcarrito);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total = $result_total->fetch_assoc()['total'] ?? 0;

// ❌ Validar si el total es válido
if ($total <= 0) {
    echo "No se puede procesar el pago, el total es 0.";
    exit();
}

$conn->begin_transaction();

try {
    // ✅ 3. Insertar en pedidos
    $sql_insert_pedido = "INSERT INTO pedidos (fecha_pedido, estado_pedido, total, cantidad, clientes_idclientes)
                          VALUES (NOW(), 'pendiente', ?, 1, ?)";
    $stmt_pedido = $conn->prepare($sql_insert_pedido);
    $stmt_pedido->bind_param("di", $total, $idcliente);
    $stmt_pedido->execute();
    $idpedido = $conn->insert_id;

    // ✅ 4. Insertar en pagos
    $sql_insert_pago = "INSERT INTO pagos (fecha_pago, monto, metodo_pago, pedidos_idpedidos, clientes_idclientes)
                        VALUES (NOW(), ?, ?, ?, ?)";
    $stmt_pago = $conn->prepare($sql_insert_pago);
    $stmt_pago->bind_param("ssii", $total, $metodo_pago, $idpedido, $idcliente);
    $stmt_pago->execute();

    // ✅ 5. Insertar en ventas
    $sql_insert_venta = "INSERT INTO ventas (fecha_venta, cantidad, total, metodo_pago, pedidos_idpedidos, clientes_idclientes)
                         VALUES (NOW(), 1, ?, ?, ?, ?)";
    $stmt_venta = $conn->prepare($sql_insert_venta);
    $stmt_venta->bind_param("ssii", $total, $metodo_pago, $idpedido, $idcliente);
    $stmt_venta->execute();
    $idventa = $conn->insert_id;

    // ✅ 6. Insertar productos en ventas_has_productos
    $sql_productos = "SELECT productos_idproductos, cantidad FROM carrito_has_productos WHERE carrito_idcarrito = ?";
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->bind_param("i", $idcarrito);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();

    while ($producto = $result_productos->fetch_assoc()) {
        $sql_insert_venta_producto = "INSERT INTO ventas_has_productos (ventas_idventas, ventas_pedidos_idpedidos, ventas_clientes_idclientes, productos_idproductos, cantidad)
                                      VALUES (?, ?, ?, ?, ?)";
        $stmt_venta_producto = $conn->prepare($sql_insert_venta_producto);
        $stmt_venta_producto->bind_param("iiiii", $idventa, $idpedido, $idcliente, $producto['productos_idproductos'], $producto['cantidad']);
        $stmt_venta_producto->execute();
    }

    // ✅ 7. Actualizar carrito a 'pagado'
    $sql_update_carrito = "UPDATE carrito SET estado = 'pagado' WHERE idcarrito = ?";
    $stmt_update = $conn->prepare($sql_update_carrito);
    $stmt_update->bind_param("i", $idcarrito);
    $stmt_update->execute();

    // ✅ 8. Eliminar productos del carrito
    $sql_delete_productos = "DELETE FROM carrito_has_productos WHERE carrito_idcarrito = ?";
    $stmt_delete = $conn->prepare($sql_delete_productos);
    $stmt_delete->bind_param("i", $idcarrito);
    $stmt_delete->execute();

    $conn->commit();

    // 🎉 Éxito
    echo "<div class='alert alert-success'>✅ ¡Pago procesado exitosamente! Gracias por tu compra.</div>";
    echo "<a href='" . $pagina_origen . "' class='btn btn-primary'>← Volver a la tienda</a>";

} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Error al procesar el pago: " . $e->getMessage();
}
?>
