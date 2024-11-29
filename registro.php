<?php
// Incluye el archivo de conexión a la base de datos
require 'conexion.php';

// Habilita la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén los datos del formulario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (!empty($nombre) && !empty($correo) && !empty($contrasena)) {
        // Verifica si el correo ya está registrado
        $sql_verificar = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("s", $correo);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();

        if ($resultado_verificar->num_rows > 0) {
            // Si el correo ya está registrado
            echo "<div class='alert alert-danger'>El correo ya está registrado. Intente con otro.</div>";
        } else {
            // Si no está registrado, inserta el nuevo usuario
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Error al preparar la consulta: " . $conn->error);
            }

            $stmt->bind_param("sss", $nombre, $correo, $contrasena_hash);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Registro exitoso. Ahora puede iniciar sesión.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al registrar el usuario: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }

        $stmt_verificar->close();
    } else {
        echo "<div class='alert alert-danger'>Por favor, complete todos los campos.</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Vida y Salud</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ffffff;
            color: #28a745;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 500px;
        }
        header {
            background-color: #28a745;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .btn-secondary {
            margin-top: 10px;
        }
        .text-center a {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Registro de Usuario</h2>
        <div class="text-center mb-4">
            <a href="index.html" class="btn btn-secondary">Regresar al Inicio</a>
        </div>
        <form method="POST" action="registro.php">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
            <div class="mt-3">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </form>
    </div>
</body>
</html>
