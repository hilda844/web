<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['contraseña'] ?? '';

// Validación básica
if (empty($usuario) || empty($password)) {
    echo "<script>alert('⚠️ Completa todos los campos'); window.location.href = 'login.php';</script>";
    exit;
}

// Verificar si el usuario existe
$sql = "SELECT * FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 1) {
    $row = $resultado->fetch_assoc();

    // Validar la contraseña en texto plano
    if ($password === $row['contraseña']) {  // Comparar contraseñas en texto plano

        // Guardar sesión con el id de cliente y otros detalles
        $_SESSION['id_usuario'] = $row['idusuarios']; // Almacenar idusuarios en la sesión
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['tipo'] = $row['tipo'];  // Guardamos el tipo (cliente o admin)

        // Verificar si es un cliente
        if ($row['tipo'] == 3) {  // Si es un cliente
            // Obtener el idcliente desde la tabla clientes, ya que la relación está en idusuarios
            $sql_cliente = "SELECT idclientes FROM clientes WHERE idusuarios = ? LIMIT 1";
            $stmt2 = $conn->prepare($sql_cliente);
            $stmt2->bind_param("i", $row['idusuarios']);
            $stmt2->execute();
            $res_cliente = $stmt2->get_result();

            if ($res_cliente->num_rows > 0) {
                $cliente = $res_cliente->fetch_assoc();
                $_SESSION['id_cliente'] = $cliente['idclientes'];  // Almacenar idclientes en la sesión
            }

            // Redirigir al cliente
            header("Location: tienda.php");
            exit;
        }

        // Si es administrador
        if ($row['tipo'] == 1 || $row['tipo'] == 2) {
            header("Location: administrador/panel.php");  // Redirige al panel del administrador
            exit;
        }

    } else {
        echo "<script>alert('❌ Contraseña incorrecta'); window.location.href = 'login.php';</script>";
        exit;
    }

} else {
    echo "<script>alert('❌ Usuario no encontrado'); window.location.href = 'login.php';</script>";
    exit;
}
?>
