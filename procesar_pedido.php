<?php
$precio_chipa = 600;
$fecha_actual = date('d-m-y');
$pedidos_file = "historial/$fecha_actual.txt";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $cantidad = intval($_POST["cantidad"]);
    $ip = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario
    
    // Validar datos
    if (!empty($nombre) && $cantidad >= 1 && $cantidad <= 10) {
        // Guardar pedido con IP
        $pedido = "$nombre|$cantidad|$ip\n";
        file_put_contents($pedidos_file, $pedido, FILE_APPEND);
    }
}

// Redirigir de vuelta a la página principal
header("Location: index.php");
exit();
?>