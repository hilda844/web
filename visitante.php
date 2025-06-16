<?php
session_start();
$_SESSION['nombre'] = 'Visitante';
$_SESSION['rol'] = 'Visitante';
$_SESSION['tipo'] = 0; // Tipo 0 = visitante

header("Location: tienda.php");
exit();
