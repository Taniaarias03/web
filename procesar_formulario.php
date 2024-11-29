<?php
include 'conexion.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$Nombre = $_POST['from_name'];
$Correo = $_POST['from_email'];
$Telefono = $_POST['phone'];
$Mensaje = $_POST['message'];

if (empty($Nombre) || empty($Correo) || empty($Telefono) || empty($Mensaje)) {
    echo json_encode(["error" => "Error: Uno o más campos están vacíos."]);
    exit;
}

$sql = "INSERT INTO contactos (nombre, correo, telefono, mensaje) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

if (!$stmt->bind_param("ssss", $Nombre, $Correo, $Telefono, $Mensaje)) {
    echo json_encode(["error" => "Error en la vinculación de parámetros: " . $stmt->error]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(["success" => "Contacto guardado exitosamente."]);
} else {
    echo json_encode(["error" => "Error al guardar el contacto: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
