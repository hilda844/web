<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
    $id_pedido = $_POST['id_pedido'];
    $comprobante = $_FILES['comprobante'];

    // Verificar si el archivo fue cargado correctamente
    if ($comprobante['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $comprobante['name'];
        $ruta_temporal = $comprobante['tmp_name'];
        $directorio_destino = "comprobantes/";

        // Mover archivo a la carpeta de destino
        if (move_uploaded_file($ruta_temporal, $directorio_destino . $nombre_archivo)) {
            // Guardar la ruta en la base de datos
            $sql = "UPDATE pedidos SET comprobante_pago = '$nombre_archivo' WHERE idpedidos = $id_pedido";
            if (mysqli_query($conn, $sql)) {
                echo "Comprobante subido correctamente.";
                header("Location: ver_pedido.php?id_pedido=" . $id_pedido);
                exit();
            } else {
                echo "Error al actualizar el pedido.";
            }
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "Error al subir el archivo.";
    }
}
?>
