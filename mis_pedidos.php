<?php
// Iniciar sesión para poder acceder a las variables de sesión
session_start();

// Conexión a la base de datos
include("conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Si no está autenticado, redirigir a la página de login o mostrar mensaje de error
    echo "Acceso no autorizado. Inicia sesión.";
    exit;
}

// Obtener el ID del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Realizar la consulta para obtener los pedidos del usuario
$query = "SELECT * FROM pedidos WHERE clientes_idclientes = $id_usuario";
$result = mysqli_query($conn, $query);

// Verificar si hay pedidos
if ($result) {
    echo '<h2>Mis Pedidos</h2>';
    echo '<table class="table">';
    echo '<tr><th>Fecha</th><th>Estado</th><th>Total</th><th>Comprobante</th><th>Acciones</th></tr>';

    // Recorrer los pedidos y mostrarlos
    while ($pedido = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $pedido['fecha_pedido'] . '</td>';
        echo '<td>' . $pedido['estado_pedido'] . '</td>';
        echo '<td>' . $pedido['total'] . '</td>';
        echo '<td>';

        // Verificar si ya existe un comprobante de pago
        if ($pedido['comprobante_pago']) {
            echo '<a href="comprobantes/' . $pedido['comprobante_pago'] . '" target="_blank">Ver Comprobante</a>';
        } else {
            echo 'No se ha subido comprobante';
        }
        echo '</td>';
        echo '<td><a href="ver_pedido.php?id_pedido=' . $pedido['idpedidos'] . '">Ver Pedido</a></td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo "No se encontraron pedidos para este usuario.";
}
?>
