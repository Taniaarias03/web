<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Verificar si el usuario existe en la base de datos
    $sql = "SELECT id_usuario, nombre, contrasena FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
           
            $_SESSION['nombre'] = $usuario['nombre'];

            echo "<div class='alert alert-success'>Inicio de sesión correcto. Redirigiendo...</div>";
            header('refresh:2;url=ventas.php');
            exit;
        } else {
            echo "<div class='alert alert-danger'>Contraseña incorrecta.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Correo no registrado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Vida y Salud</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Iniciar Sesión</h2>
        <form action="login.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" name="correo" id="correo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
        <div class="mt-3">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
        </div>
    </div>
</body>
</html>
