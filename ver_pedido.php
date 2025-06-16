<?php
include("conexion.php");

if (isset($_GET['id_pedido'])) {
    $id_pedido = $_GET['id_pedido'];

    // Obtener el estado del pedido y el comprobante de pago
    $query_estado = "SELECT estado_pedido, comprobante_pago FROM pedidos WHERE idpedidos = $id_pedido";
    $resultado_estado = mysqli_query($conn, $query_estado);
    $pedido = mysqli_fetch_assoc($resultado_estado);

    if ($pedido) {
        // Obtener los productos relacionados con el pedido
        $query_productos = "SELECT p.nombre, pp.cantidad, pp.precios 
                            FROM productos p 
                            JOIN pedidos_has_productos pp ON p.idproductos = pp.idproductos 
                            WHERE pp.pedidos_idpedidos = $id_pedido";
        $resultado_productos = mysqli_query($conn, $query_productos);

        // Mostrar el estado y los productos del pedido
        echo '<h2>Detalles del Pedido #' . $id_pedido . '</h2>';
        echo '<p><strong>Estado del Pedido:</strong> ' . $pedido['estado_pedido'] . '</p>';

        // Comprobante de pago
        if ($pedido['comprobante_pago']) {
            echo '<p><strong>Comprobante de Pago:</strong> <a href="comprobantes/' . $pedido['comprobante_pago'] . '" target="_blank">Ver Comprobante</a></p>';
        } else {
            echo '<p><strong>Comprobante de Pago:</strong> No se ha subido ningún comprobante aún.</p>';
        }

        // Productos del pedido
        echo '<h3>Productos del Pedido</h3>';
        echo '<table class="table">';
        echo '<tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>';

        while ($producto = mysqli_fetch_assoc($resultado_productos)) {
            $total_producto = $producto['cantidad'] * $producto['precio'];
            echo '<tr>';
            echo '<td>' . $producto['nombre'] . '</td>';
            echo '<td>' . $producto['cantidad'] . '</td>';
            echo '<td>' . $producto['precio'] . '</td>';
            echo '<td>' . $total_producto . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>Pedido no encontrado.</p>';
    }
}
?>
