<?php
include('../includes/db.php');
// Eliminar cita
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM citas WHERE id_cita=$id") or die($conn->error);
    header("Location: reportecitas.php");
}

// Editar cita
if (isset($_POST['update'])) {
    $id = $_POST['id_cita'];
    $dni_paciente = $_POST['dni_paciente'];
    $fecha_cita = $_POST['fecha_cita'];
    $hora_cita = $_POST['hora_cita'];
    $dni_psicologo = $_POST['dni_psicologo'];
    $estado = $_POST['estado'];
    $notas = $_POST['notas'];

    $conn->query("UPDATE citas SET dni_paciente='$dni_paciente', fecha_cita='$fecha_cita', hora_cita='$hora_cita', dni_psicologo='$dni_psicologo', estado='$estado', notas='$notas' WHERE id_cita=$id") or die($conn->error);
    header("Location: reportecitas.php");
}

// Obtener citas
$result = $conn->query("SELECT * FROM citas") or die($conn->error);

// Verificar si la tabla está vacía
if ($result->num_rows == 0) {
    // Reiniciar el contador del auto-incremento
    $conn->query("ALTER TABLE citas AUTO_INCREMENT = 1") or die($conn->error);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reportecitas.css?v=1.3">
    <title>Reporte de Citas</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Sidebar contenido -->
            <a href="paciente.php"><img src="img/register_patient.png" alt="Registrar pacientes" class="icon">Registrar pacientes</a>
            <a href="citas.php"><img src="img/register_appointment.png" alt="Registrar cita" class="icon">Registrar cita</a>
            <a href="historiaclinica.php"><img src="img/clinical_history.png" alt="Historia clínica" class="icon">Historia clínica</a>
            <a href="pruebas.php"><img src="img/tests_done.png" alt="Pruebas realizadas" class="icon">Pruebas realizadas</a>
            <a href="reportecitas.php"><img src="img/appointment_report.png" alt="Reporte de citas" class="icon">Reporte de citas</a>
            <a href="pacientes.php"><img src="img/appointment_report.png" alt="Reporte de pacientes" class="icon">Reporte de pacientes</a>
            <a href="usuarios.php"><img src="img/register_user.png" alt="Registrar usuarios" class="icon">Registrar usuarios</a>
            <img src="img/Logo.png" alt="Logo" class="logo">
        </div>
        <div class="main-content">
            <button class="logout-button" onclick="location.href='logout.php'">Cerrar Sesión</button>
            <div class="form-container">
                <h2>Reporte de Citas</h2>
                <table id="citas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI Paciente</th>
                            <th>Fecha de Cita</th>
                            <th>Hora de Cita</th>
                            <th>DNI Psicólogo</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_cita']; ?></td>
                                <td><?php echo $row['dni_paciente']; ?></td>
                                <td><?php echo $row['fecha_cita']; ?></td>
                                <td><?php echo $row['hora_cita']; ?></td>
                                <td><?php echo $row['dni_psicologo']; ?></td>
                                <td><?php echo $row['estado']; ?></td>
                                <td><?php echo $row['notas']; ?></td>
                                <td class="buttonReport">
                                    <div>
                                        <a href="reportecitas.php?edit=<?php echo $row['id_cita']; ?>" class="edit-button">Editar</a>
                                        <a href="reportecitas.php?delete=<?php echo $row['id_cita']; ?>" class="delete-button" onclick="return confirm('¿Está seguro de eliminar esta cita?');">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar citas -->
    <?php if (isset($_GET['edit'])): 
        $id = $_GET['edit'];
        $record = $conn->query("SELECT * FROM citas WHERE id_cita=$id") or die($conn->error);
        $data = $record->fetch_assoc();
    ?>
    <div id="edit-modal" class="modal" style="display: block;">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <form action="reportecitas.php" method="POST">
                <input type="hidden" name="id_cita" value="<?php echo $data['id_cita']; ?>">
                <div>
                    <label for="dni_paciente">DNI del Paciente:</label>
                    <input type="text" name="dni_paciente" value="<?php echo $data['dni_paciente']; ?>" required>
                </div>
                <div>
                    <label for="fecha_cita">Fecha de Cita:</label>
                    <input type="date" name="fecha_cita" value="<?php echo $data['fecha_cita']; ?>" required>
                </div>
                <div>
                    <label for="hora_cita">Hora de Cita:</label>
                    <input type="time" name="hora_cita" value="<?php echo $data['hora_cita']; ?>" required>
                </div>
                <div>
                    <label for="dni_psicologo">DNI del Psicólogo:</label>
                    <input type="text" name="dni_psicologo" value="<?php echo $data['dni_psicologo']; ?>" required>
                </div>
                <div>
                    <label for="estado">Estado:</label>
                    <input type="text" name="estado" value="<?php echo $data['estado']; ?>" required>
                </div>
                <div>
                    <label for="notas">Notas:</label>
                    <input type="text" name="notas" value="<?php echo $data['notas']; ?>">
                </div>
                <button type="submit" name="update">Actualizar</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        function closeModal() {
            document.getElementById('edit-modal').style.display = 'none';
            window.location.href = 'reportecitas.php';
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>