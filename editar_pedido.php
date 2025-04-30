<?php
$fecha_actual = date('d-m-y');
$pedidos_file = "historial/$fecha_actual.txt";
$usuario_ip = $_SERVER['REMOTE_ADDR'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $index = intval($_POST["index"]);
    $nombre = trim($_POST["nombre"]);
    $cantidad = intval($_POST["cantidad"]);

    // Validar datos
    if (!empty($nombre) && $cantidad >= 1 && $cantidad <= 10) {
        // Leer todos los pedidos
        $pedidos = file($pedidos_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (isset($pedidos[$index])) {
            // Verificar que la IP coincida
            list($_, $__, $ip) = explode('|', $pedidos[$index]);
            if ($ip === $usuario_ip) {
                // Actualizar el pedido
                $pedidos[$index] = "$nombre|$cantidad|$ip";
                // Guardar los pedidos actualizados
                file_put_contents($pedidos_file, implode("\n", $pedidos) . "\n");
            }
        }
    }
}

// Redirigir de vuelta a la página principal
header("Location: index.php");
exit();
?>