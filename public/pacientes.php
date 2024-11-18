<?php
include('../includes/db.php');

// Eliminar paciente
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pacientes WHERE id_paciente=$id") or die($conn->error);
    header("Location: pacientes.php");
}

// Editar paciente
if (isset($_POST['update'])) {
    $id_paciente = $_POST['id_paciente'];
    $dni_paciente = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado_civil = $_POST['estado_civil'];
    $ocupacion = $_POST['ocupacion'];
    $numero_hijos = $_POST['numero_hijos'];
    $grado_instruccion = $_POST['grado_instruccion'];

    $conn->query(
        "UPDATE pacientes SET dni='$dni_paciente', nombre='$nombre', apellidos='$apellidos', email='$email', telefono='$telefono', direccion='$direccion',
        fecha_nacimiento='$fecha_nacimiento', estado_civil='$estado_civil', ocupacion='$ocupacion', numero_hijos='$numero_hijos', grado_instruccion='$grado_instruccion' 
        WHERE id_paciente=$id_paciente") or die($conn->error);
    header("Location: pacientes.php");
}

// Obtener pacientes
$result = $conn->query("SELECT * FROM pacientes") or die($conn->error);


// Verificar si la tabla está vacía
if ($result->num_rows == 0) {
    // Reiniciar el contador del auto-incremento
    $conn->query("ALTER TABLE pacientes AUTO_INCREMENT = 1") or die($conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pacientes.css?v=1.3">
    <title>Reporte de Pacientes</title>
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
                <h2>Reporte de Pacientes</h2>
                <div class="table-container">
                    <table id="citas-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DNI</th>
                                <th>NOMBRE</th>
                                <th>APELLIDOS</th>
                                <th>EMAIL</th>
                                <th>TELEFONO</th>
                                <th>DIRECCION</th>
                                <th>FECHA NACIMIENTO</th>
                                <th>ESTADO CIVIL</th>
                                <th>OCUPACION</th>
                                <th>Nº HIJOS</th>
                                <th>GRADO INSTRUCCION</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id_paciente']; ?></td>
                                    <td><?php echo $row['dni']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['apellidos']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['telefono']; ?></td>
                                    <td><?php echo $row['direccion']; ?></td>
                                    <td><?php echo $row['fecha_nacimiento']; ?></td>
                                    <td><?php echo $row['estado_civil']; ?></td>
                                    <td><?php echo $row['ocupacion']; ?></td>
                                    <td><?php echo $row['numero_hijos']; ?></td>
                                    <td><?php echo $row['grado_instruccion']; ?></td>
                                    <td class="buttonReport">
                                        <div>
                                        <a href="pacientes.php?edit=<?php echo $row['id_paciente']; ?>" class="edit-button">Editar</a>
                                        <a href="pacientes.php?delete=<?php echo $row['id_paciente']; ?>" class="delete-button" onclick="return confirm('¿Está seguro de eliminar este paciente?');">Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar pacientes -->
    <?php if (isset($_GET['edit'])): 
        $id = $_GET['edit'];
        $record = $conn->query("SELECT * FROM pacientes WHERE id_paciente=$id") or die($conn->error);
        $data = $record->fetch_assoc();
    ?>
    <div id="edit-modal" class="modal" style="display: block;">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <form id="form-editar" action="pacientes.php" method="POST">
                <input type="hidden" name="id_paciente" value="<?php echo $data['id_paciente']; ?>">
                <div>
                    <label for="dni">DNI:</label>
                    <input type="text" name="dni" value="<?php echo $data['dni']; ?>" required>
                </div>
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo $data['nombre']; ?>" required>
                </div>
                <div>
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" name="apellidos" value="<?php echo $data['apellidos']; ?>" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $data['email']; ?>" required>
                </div>
                <div>
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo $data['telefono']; ?>" required>
                </div>
                <div>
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" value="<?php echo $data['direccion']; ?>" required>
                </div>
                <div>
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" value="<?php echo $data['fecha_nacimiento']; ?>" required>
                </div>
                <div>
                    <label for="estado_civil">Estado Civil:</label>
                    <input type="text" name="estado_civil" value="<?php echo $data['estado_civil']; ?>" required>
                </div>
                <div>
                    <label for="ocupacion">Ocupación:</label>
                    <input type="text" name="ocupacion" value="<?php echo $data['ocupacion']; ?>" required>
                </div>
                <div>
                    <label for="numero_hijos">Número de Hijos:</label>
                    <input type="number" name="numero_hijos" value="<?php echo $data['numero_hijos']; ?>" required>
                </div>
                <div>
                    <label for="grado_instruccion">Grado de Instrucción:</label>
                    <input type="text" name="grado_instruccion" value="<?php echo $data['grado_instruccion']; ?>" required>
                </div>
                <button type="submit" name="update">Actualizar</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        function closeModal() {
            document.getElementById('edit-modal').style.display = 'none';
            window.location.href = 'pacientes.php';
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
