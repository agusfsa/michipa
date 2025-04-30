<?php
$fecha_actual = date('d-m-y');
$pedidos_file = "historial/$fecha_actual.txt";
$usuario_ip = $_SERVER['REMOTE_ADDR'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $index = intval($_POST["index"]);

    // Leer todos los pedidos
    $pedidos = file($pedidos_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (isset($pedidos[$index])) {
        // Verificar que la IP coincida
        $partes = explode('|', $pedidos[$index]);
        $ip = isset($partes[2]) ? $partes[2] : '0.0.0.0'; // IP por defecto para pedidos antiguos
        if ($ip === $usuario_ip || $ip === '0.0.0.0') {
            // Eliminar el pedido
            unset($pedidos[$index]);
            // Guardar los pedidos actualizados
            file_put_contents($pedidos_file, implode("\n", array_filter($pedidos)) . "\n");
        }
    }
}

// Redirigir de vuelta a la página principal
header("Location: index.php");
exit();
?>