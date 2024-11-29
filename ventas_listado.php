<?php
// Include the database connection
require 'conexion.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['id_usuario'])) {
    echo "<div class='alert alert-danger'>You must log in to view the sales.</div>";
    echo "<a href='login.php' class='btn btn-primary'>Log In</a>";
    exit;
}

$id_usuario = $_SESSION['id_usuario']; // Logged-in user ID

// Get the list of sales made by the user, including the price per product and total price
$sql = "SELECT ventas.id_venta, productos.nombre_producto, productos.precio, ventas.cantidad, 
               (productos.precio * ventas.cantidad) AS total_venta, ventas.fecha_venta
        FROM ventas 
        INNER JOIN productos ON ventas.id_producto = productos.id_producto
        WHERE ventas.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Ventas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Ventas Realizadas</h2>
        <div class="text-center mb-4">
            <a href="ventas.php" class="btn btn-secondary">Regresar a Ventas</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th># Venta</th>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Precio Total</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($venta = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $venta['id_venta']; ?></td>
                        <td><?= $venta['nombre_producto']; ?></td>
                        <td>$<?= number_format($venta['precio'], 2); ?></td>
                        <td><?= $venta['cantidad']; ?></td>
                        <td>$<?= number_format($venta['total_venta'], 2); ?></td>
                        <td><?= $venta['fecha_venta']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
