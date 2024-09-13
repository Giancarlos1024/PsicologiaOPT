<?php
include('../includes/db.php');

session_start();

if (!isset($_SESSION['dni'])) {
    header('Location: ../index.php');
    exit();
}

// Obtener datos del usuario logueado
$dni = $_SESSION['dni'];
$sql = "SELECT * FROM usuarios WHERE dni = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <a href="paciente.php"><img src="img/register_patient.png" alt="Registrar pacientes" class="icon">Registrar pacientes</a>
            <a href="citas.php"><img src="img/register_appointment.png" alt="Registrar cita" class="icon">Registrar cita</a>
            <a href="historiaclinica.php"><img src="img/clinical_history.png" alt="Historia clínica" class="icon">Historia clínica</a>
            <a href="pruebas.php"><img src="img/tests_done.png" alt="Pruebas realizadas" class="icon">Pruebas realizadas</a>
            <a href="reportecitas.php"><img src="img/appointment_report.png" alt="Reporte de citas" class="icon">Reporte de citas</a>
            <a href="usuarios.php"><img src="img/register_user.png" alt="Registrar usuarios" class="icon">Registrar usuarios</a>
            <img src="img/Logo.png" alt="Logo" class="logo">
        </div>
        <div class="main-content">
            <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?>!</h1>
            <p><strong>DNI:</strong> <?php echo htmlspecialchars($user['dni']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Rol:</strong> <?php echo htmlspecialchars($user['rol']); ?></p>
            <button class="logout-button" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
        </div>
    </div>
</body>
</html>
