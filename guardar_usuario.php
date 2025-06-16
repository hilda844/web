<?php 
include("conexion.php");

// Recolectar datos del formulario
$nombre     = $_POST['nombre'];
$ap_p       = $_POST['apellido_paterno'];
$ap_m       = $_POST['apellido_materno'];
$ciudad     = $_POST['ciudad'];
$telefono   = $_POST['telefono'];
$contrasena = substr($_POST['contraseña'], 0, 30); // Limitar a 30 caracteres
$rol        = $_POST['rol'];  // Asignamos el rol del formulario
$tipo       = ($rol == 'administrador') ? 1 : 3;  // Determinamos el tipo según el rol
$fecha      = date("Y-m-d H:i:s");  // Fecha de creación

// Insertar en tabla 'usuarios'
$sql_usuario = "INSERT INTO usuarios 
(nombre, apellido_paterno, apellido_materno, telefono, contraseña, tipo, fecha_creacion) 
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt_usuario = mysqli_prepare($conn, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "sssssis", $nombre, $ap_p, $ap_m, $telefono, $contrasena, $tipo, $fecha);

if (mysqli_stmt_execute($stmt_usuario)) {
    // Obtener el ID del usuario recién insertado
    $id_usuario = mysqli_insert_id($conn);

    // Insertar en tabla 'clientes' solo si el rol es 'cliente'
    if ($tipo == 3) {  // Solo insertamos en 'clientes' si es un cliente
        $sql_cliente = "INSERT INTO clientes 
        (idusuarios, nombre, apellido_paterno, apellido_materno, ciudad, num_calle, telefono) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
        mysqli_stmt_bind_param($stmt_cliente, "issssss", $id_usuario, $nombre, $ap_p, $ap_m, $ciudad, $telefono, $ciudad);

        if (mysqli_stmt_execute($stmt_cliente)) {
            echo "<script>alert('✅ Cuenta creada correctamente'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('❌ Error al registrar cliente: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('✅ Cuenta de administrador creada correctamente'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('❌ Error al registrar usuario: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

// Cerrar
mysqli_stmt_close($stmt_usuario);
mysqli_stmt_close($stmt_cliente);
mysqli_close($conn);
?>
