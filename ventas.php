<?php
// Incluir la conexión a la base de datos
require 'conexion.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo "<div class='alert alert-danger'>Debe iniciar sesión para realizar una venta.</div>";
    echo "<div class='text-center mb-4'>
            <a href='login.php' class='btn btn-primary'>Iniciar Sesión</a>
          </div>";
    exit;
}

$id_usuario = $_SESSION['id_usuario']; // ID del usuario logueado

// Obtener la lista de productos con stock disponible
$sql_productos = "SELECT id_producto, nombre_producto, precio, stock FROM productos WHERE stock > 0";
$resultado_productos = $conn->query($sql_productos);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Procesar la venta
    $id_producto = intval($_POST['id_producto']);
    $cantidad_vendida = intval($_POST['cantidad']);

    // Obtener detalles del producto seleccionado
    $sql_detalle = "SELECT nombre_producto, precio, stock FROM productos WHERE id_producto = ?";
    $stmt = $conn->prepare($sql_detalle);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();

    if ($producto) {
        if ($cantidad_vendida > 0 && $cantidad_vendida <= $producto['stock']) {
            // Registrar la venta
            $sql_venta = "INSERT INTO ventas (id_usuario, id_producto, cantidad, fecha_venta) VALUES (?, ?, ?, ?)";
            $stmt_venta = $conn->prepare($sql_venta);
            $fecha_venta = date("Y-m-d H:i:s");
            $stmt_venta->bind_param("iiis", $id_usuario, $id_producto, $cantidad_vendida, $fecha_venta);

            if ($stmt_venta->execute()) {
                // Actualizar el stock del producto
                $nuevo_stock = $producto['stock'] - $cantidad_vendida;
                $sql_actualizar = "UPDATE productos SET stock = ? WHERE id_producto = ?";
                $stmt_actualizar = $conn->prepare($sql_actualizar);
                $stmt_actualizar->bind_param("ii", $nuevo_stock, $id_producto);
                $stmt_actualizar->execute();

                echo "<div class='alert alert-success'>Venta realizada exitosamente. Stock actualizado.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al registrar la venta.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Cantidad no válida o stock insuficiente.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    }
}

?>

<!-- Formulario para seleccionar producto y realizar la venta -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta - Vida y Salud</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Realizar Venta</h2>
        <div class="text-center mb-4">
            <a href="ventas_listado.php" class="btn btn-secondary">Ventas realizadas</a>
            <a href="index.html" class="btn btn-secondary">Regresar al Inicio</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>

        <?php if ($resultado_productos->num_rows > 0): ?>
            <form method="POST" action="">
    <div class="form-group">
        <label for="id_producto">Producto:</label>
        <select name="id_producto" id="id_producto" class="form-control" required>
            <?php while ($producto = $resultado_productos->fetch_assoc()) : ?>
                <option value="<?= $producto['id_producto'] ?>">
                    <?= $producto['nombre_producto'] ?> - Precio: $<?= $producto['precio'] ?> - Stock: <?= $producto['stock'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
    </div>
    <button type="submit" class="btn btn-primary">Realizar Venta</button>
</form>

        <?php else: ?>
            <div class="alert alert-warning">No hay productos disponibles en stock.</div>
        <?php endif; ?>
    </div>
</body>
</html>
