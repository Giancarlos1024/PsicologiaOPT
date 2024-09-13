<?php
session_start();
include('../includes/db.php');

// Obtener los datos del formulario
$dni = $_POST['dni'];
$password = $_POST['password'];

// Consulta para verificar el usuario
$sql = "SELECT * FROM usuarios WHERE dni = ? AND contrasena = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $dni, $password);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró un usuario
if ($result->num_rows === 1) {
    // Usuario autenticado
    $_SESSION['dni'] = $dni;
    header('Location: dashboard.php'); // Redirige a la página de inicio después del inicio de sesión
    exit();
} else {
    // Usuario no encontrado
    echo "DNI o contraseña incorrectos.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
