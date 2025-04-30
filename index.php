<!DOCTYPE html>
<html>
<head>
    <title>Pedidos de Chipa</title>
    <link rel="icon" type="image/x-icon" href="Chipaa-ico.ico">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php
        // Obtener la fecha actual en formato DD-MM-YY
        $fecha_actual = date('d-m-y');
        $fecha_formateada = date('d/m/Y');
        $usuario_ip = $_SERVER['REMOTE_ADDR'];
        ?>
        <div class="fecha-actual"><?php echo $fecha_formateada; ?></div>

        <h1>Pedidos de Chipa</h1>
        <p>Precio actual por chipa: $600</p>
        <button class="button" onclick="openForm()">¡Quiero mi chipa!</button>

        <h2>Lista de Pedidos</h2>
        <?php
        $precio_chipa = 600;
        $pedidos_file = "historial/$fecha_actual.txt";
        $total_general = 0;
        $total_chipas = 0;

        if (file_exists($pedidos_file)) {
            $pedidos = file($pedidos_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!empty($pedidos)) {
                echo "<table>";
                echo "<tr><th>Nombre</th><th>Cantidad</th><th>Total</th><th>Acción</th></tr>";
                foreach ($pedidos as $index => $pedido) {
                    $partes = explode('|', $pedido);
                    $nombre = $partes[0];
                    $cantidad = $partes[1];
                    // Verificar si el pedido tiene IP (formato nuevo) o no (formato antiguo)
                    $ip = isset($partes[2]) ? $partes[2] : '0.0.0.0'; // IP por defecto si no existe
                    $total = $cantidad * $precio_chipa;
                    $total_general += $total;
                    $total_chipas += (int)$cantidad;
                    echo "<tr>";
                    echo "<td>$nombre</td><td>$cantidad</td><td>$$total</td>";
                    // Mostrar botones Editar y Eliminar solo si la IP coincide
                    if ($ip === $usuario_ip || $ip === '0.0.0.0') { // Permitir edición/eliminación para pedidos antiguos
                        echo "<td>";
                        echo "<button class='button edit-button' onclick=\"openEditForm($index, '$nombre', $cantidad)\">Editar</button>";
                        echo " <button class='button delete-button' onclick=\"deletePedido($index)\">Eliminar</button>";
                        echo "</td>";
                    } else {
                        echo "<td>-</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                echo "<div class='total-general'>";
                echo "Total Chipas: $total_chipas<br>";
                echo "Total General: $$total_general";
                echo "</div>";
            } else {
                echo "<p>No hay pedidos aún.</p>";
            }
        } else {
            echo "<p>No hay pedidos aún.</p>";
        }
        ?>

        <!-- Formulario para agregar pedido -->
        <div class="form-popup" id="chipaForm">
            <form action="procesar_pedido.php" method="post">
                <h2>Pedir Chipa</h2>
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
                
                <label>Cantidad (1-10):</label>
                <input type="number" name="cantidad" min="1" max="10" required>
                
                <label>Total: $<span id="total">0</span></label>
                
                <input type="submit" class="button" value="Pedir Chipa">
                <button type="button" class="button cancel-button" onclick="closeForm()">Cancelar</button>
            </form>
        </div>

        <!-- Formulario para editar pedido -->
        <div class="form-popup" id="editChipaForm">
            <form action="editar_pedido.php" method="post">
                <h2>Editar Pedido</h2>
                <input type="hidden" name="index" id="editIndex">
                <label>Nombre:</label>
                <input type="text" name="nombre" id="editNombre" required>
                
                <label>Cantidad (1-10):</label>
                <input type="number" name="cantidad" id="editCantidad" min="1" max="10" required>
                
                <label>Total: $<span id="editTotal">0</span></label>
                
                <input type="submit" class="button" value="Guardar Cambios">
                <button type="button" class="button cancel-button" onclick="closeEditForm()">Cancelar</button>
            </form>
        </div>

        <footer>© Agustin Figueroa</footer>
    </div>

    <script>
        function openForm() {
            document.getElementById("chipaForm").style.display = "block";
        }

        function closeForm() {
            document.getElementById("chipaForm").style.display = "none";
        }

        function openEditForm(index, nombre, cantidad) {
            document.getElementById("editChipaForm").style.display = "block";
            document.getElementById("editIndex").value = index;
            document.getElementById("editNombre").value = nombre;
            document.getElementById("editCantidad").value = cantidad;
            updateEditTotal(cantidad);
        }

        function closeEditForm() {
            document.getElementById("editChipaForm").style.display = "none";
        }

        function updateEditTotal(cantidad) {
            if (cantidad >= 1 && cantidad <= 10) {
                let total = cantidad * <?php echo $precio_chipa; ?>;
                document.getElementById("editTotal").textContent = total;
            }
        }

        function deletePedido(index) {
            if (confirm("¿Estás seguro de que deseas eliminar este pedido?")) {
                // Crear un formulario dinámico para enviar la solicitud de eliminación
                let form = document.createElement("form");
                form.method = "POST";
                form.action = "eliminar_pedido.php";
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "index";
                input.value = index;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.querySelector('input[name="cantidad"]').addEventListener('input', function() {
            let cantidad = this.value;
            if (cantidad >= 1 && cantidad <= 10) {
                let total = cantidad * <?php echo $precio_chipa; ?>;
                document.getElementById("total").textContent = total;
            }
        });

        document.querySelector('#editCantidad').addEventListener('input', function() {
            let cantidad = this.value;
            updateEditTotal(cantidad);
        });
    </script>
</body>
</html>