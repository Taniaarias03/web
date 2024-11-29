<?php
// Incluir la conexión a la base de datos
require 'conexion.php';

// Si se ha enviado el formulario para agregar un nuevo producto
if (isset($_POST['nuevo_nombre_producto'])) {
    $nuevo_nombre_producto = $_POST['nuevo_nombre_producto'];
    $nuevo_descripcion = $_POST['nuevo_descripcion'];
    $nuevo_precio = $_POST['nuevo_precio'];
    $nuevo_stock = $_POST['nuevo_stock'];

    // Insertar el nuevo producto en la base de datos
    $sql_insertar = "INSERT INTO productos (nombre_producto, descripcion, precio, stock) VALUES (?, ?, ?, ?)";
    $stmt_insertar = $conn->prepare($sql_insertar);
    $stmt_insertar->bind_param("ssdi", $nuevo_nombre_producto, $nuevo_descripcion, $nuevo_precio, $nuevo_stock);
    $stmt_insertar->execute();
    echo "<div class='alert alert-success'>Nuevo producto agregado exitosamente.</div>";
}

// Si se ha enviado el formulario para modificar un producto
if (isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Actualizar el producto en la base de datos
    $sql_actualizar = "UPDATE productos SET nombre_producto = ?, descripcion = ?, precio = ?, stock = ? WHERE id_producto = ?";
    $stmt_actualizar = $conn->prepare($sql_actualizar);
    $stmt_actualizar->bind_param("ssdii", $nombre_producto, $descripcion, $precio, $stock, $id_producto);
    $stmt_actualizar->execute();
    echo "<div class='alert alert-success'>Producto actualizado exitosamente.</div>";
}

// Si se ha enviado una solicitud para eliminar un producto
if (isset($_GET['eliminar'])) {
    $id_producto = $_GET['eliminar'];

    // Eliminar el producto de la base de datos
    $sql_eliminar = "DELETE FROM productos WHERE id_producto = ?";
    $stmt_eliminar = $conn->prepare($sql_eliminar);
    $stmt_eliminar->bind_param("i", $id_producto);
    $stmt_eliminar->execute();
    echo "<div class='alert alert-danger'>Producto eliminado del inventario.</div>";
}

// Obtener todos los productos
$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Vida y Salud</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ffffff;
            color: #28a745;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Inventario de Productos</h2>
        <div class="text-center mb-4">
    <a href="index.html" class="btn btn-secondary">Regresar al Inicio</a>
</div>
        <!-- Formulario para agregar un nuevo producto -->
        <h3>Agregar Nuevo Producto</h3>
        <form method="POST" action="inventario.php">
            <div class="mb-3">
                <label for="nuevo_nombre_producto" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nuevo_nombre_producto" id="nuevo_nombre_producto" required>
            </div>
            <div class="mb-3">
                <label for="nuevo_descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" name="nuevo_descripcion" id="nuevo_descripcion" required></textarea>
            </div>
            <div class="mb-3">
                <label for="nuevo_precio" class="form-label">Precio</label>
                <input type="number" class="form-control" name="nuevo_precio" id="nuevo_precio" required step="0.01">
            </div>
            <div class="mb-3">
                <label for="nuevo_stock" class="form-label">Stock</label>
                <input type="number" class="form-control" name="nuevo_stock" id="nuevo_stock" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </form>

        <!-- Tabla de productos -->
        <h3 class="mt-4">Lista de Productos</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $producto['id_producto']; ?></td>
                        <td><?= $producto['nombre_producto']; ?></td>
                        <td><?= $producto['descripcion']; ?></td>
                        <td>$<?= number_format($producto['precio'], 2); ?></td>
                        <td><?= $producto['stock']; ?></td>
                        <td>
                            <a href="inventario.php?id=<?= $producto['id_producto']; ?>" class="btn btn-warning">Editar</a>
                            <a href="inventario.php?eliminar=<?= $producto['id_producto']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['id'])): ?>
            <?php
            $id_producto = $_GET['id'];
            $sql_editar = "SELECT * FROM productos WHERE id_producto = ?";
            $stmt_editar = $conn->prepare($sql_editar);
            $stmt_editar->bind_param("i", $id_producto);
            $stmt_editar->execute();
            $producto = $stmt_editar->get_result()->fetch_assoc();
            ?>
            <!-- Formulario de edición de producto -->
            <h3>Editar Producto</h3>
            <form method="POST" action="inventario.php">
                <input type="hidden" name="id_producto" value="<?= $producto['id_producto']; ?>">
                <div class="mb-3">
                    <label for="nombre_producto" class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre_producto" id="nombre_producto" value="<?= $producto['nombre_producto']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" id="descripcion" required><?= $producto['descripcion']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" class="form-control" name="precio" id="precio" value="<?= $producto['precio']; ?>" required step="0.01">
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" name="stock" id="stock" value="<?= $producto['stock']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
