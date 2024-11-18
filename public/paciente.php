<?php
session_start();
if (!isset($_SESSION['dni'])) {
    header('Location: ../index.php');
    exit();
}

include('../includes/db.php');

// Inicializar variables
$nombre = $apellidos = $email = $telefono = $direccion = $fecha_nacimiento = "";
$dni = ""; // Inicializar $dni
$estado_civil = $ocupacion = $numero_hijos = $grado_instruccion = ""; // Nuevas variables
$mensaje = ""; // Variable para mensajes

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    if (isset($_POST['dni'])) {
        $dni = $_POST['dni'];
    }

    switch ($action) {
        case 'create':
            // Validación básica
            if (empty($dni) || empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['email'])) {
                $mensaje = "Todos los campos requeridos deben ser completados.";
            } else {
                // Sentencia preparada para prevenir inyección SQL
                $stmt = $conn->prepare("INSERT INTO pacientes (dni, nombre, apellidos, email, telefono, direccion, fecha_nacimiento, estado_civil, ocupacion, numero_hijos, grado_instruccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssi", $dni, $_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['telefono'], $_POST['direccion'], $_POST['fecha_nacimiento'], $_POST['estado_civil'], $_POST['ocupacion'], $_POST['numero_hijos'], $_POST['grado_instruccion']);

                if ($stmt->execute()) {
                    $mensaje = "Nuevo paciente registrado correctamente.";
                } else {
                    $mensaje = "Error al registrar el paciente. Por favor, intente nuevamente.";
                }
                $stmt->close();
            }
            break;

        case 'update':
            $stmt = $conn->prepare("UPDATE pacientes SET nombre=?, apellidos=?, email=?, telefono=?, direccion=?, fecha_nacimiento=?, estado_civil=?, ocupacion=?, numero_hijos=?, grado_instruccion=? WHERE dni=?");
            $stmt->bind_param("ssssssssssi", $_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['telefono'], $_POST['direccion'], $_POST['fecha_nacimiento'], $_POST['estado_civil'], $_POST['ocupacion'], $_POST['numero_hijos'], $_POST['grado_instruccion'], $dni);

            if ($stmt->execute()) {
                $mensaje = "Paciente actualizado correctamente.";
            } else {
                $mensaje = "Error al actualizar el paciente. Por favor, intente nuevamente.";
            }
            $stmt->close();
            break;

        case 'delete':
            $stmt = $conn->prepare("DELETE FROM pacientes WHERE dni=?");
            $stmt->bind_param("s", $dni);

            if ($stmt->execute()) {
                $mensaje = "Paciente eliminado correctamente.";
            } else {
                $mensaje = "Error al eliminar el paciente. Por favor, intente nuevamente.";
            }
            $stmt->close();
            break;

        case 'read':
            $stmt = $conn->prepare("SELECT * FROM pacientes WHERE dni=?");
            $stmt->bind_param("s", $dni);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Asignar valores a variables para usarlas en el formulario
                $nombre = $row["nombre"];
                $apellidos = $row["apellidos"];
                $email = $row["email"];
                $telefono = $row["telefono"];
                $direccion = $row["direccion"];
                $fecha_nacimiento = $row["fecha_nacimiento"];
                $estado_civil = $row["estado_civil"];
                $ocupacion = $row["ocupacion"];
                $numero_hijos = $row["numero_hijos"];
                $grado_instruccion = $row["grado_instruccion"];
            } else {
                $mensaje = "No se encontró el paciente.";
            }
            $stmt->close();
            break;
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pacientes</title>
    <link rel="stylesheet" href="css/paciente.css?v=2.1">
</head>
<body>
    <div class="container">
        <div class="sidebar">
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
            <form id="patient-form" method="POST" action="paciente.php">
                <h2>Registro de paciente</h2>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($dni); ?>" required>

                <button type="button" id="search-btn" onclick="buscarCitas()">Buscar</button>

                <label for="nombre">Nombres:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" required>

                <label for="email">Correo:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">

                <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>">

                <label for="estado_civil">Estado Civil:</label>
                <select id="estado_civil" name="estado_civil">
                    <option value="">Seleccione</option>
                    <option value="Soltero/a" <?php echo ($estado_civil == "Soltero/a") ? 'selected' : ''; ?>>Soltero/a</option>
                    <option value="Casado/a" <?php echo ($estado_civil == "Casado/a") ? 'selected' : ''; ?>>Casado/a</option>
                    <option value="Divorciado/a" <?php echo ($estado_civil == "Divorciado/a") ? 'selected' : ''; ?>>Divorciado/a</option>
                    <option value="Viudo/a" <?php echo ($estado_civil == "Viudo/a") ? 'selected' : ''; ?>>Viudo/a</option>
                </select>

                <label for="ocupacion">Ocupación Actual:</label>
                <input type="text" id="ocupacion" name="ocupacion" value="<?php echo htmlspecialchars($ocupacion); ?>">

                <label for="numero_hijos">Número de Hijos:</label>
                <input type="number" id="numero_hijos" name="numero_hijos" value="<?php echo htmlspecialchars($numero_hijos); ?>">

                <label for="grado_instruccion">Grado de Instrucción:</label>
                <input type="text" id="grado_instruccion" name="grado_instruccion" value="<?php echo htmlspecialchars($grado_instruccion); ?>">

                <input type="hidden" name="action" value="create">
                <button type="submit" name="action" value="create">Registrar</button>
                <button type="submit" name="action" value="update">Editar</button>
                <button type="submit" name="action" value="delete">Eliminar</button>
            </form>

            </div>
        </div>
    </div>

    <script>
        function buscarCitas() {
            var dni = document.getElementById('dni').value;
            if (dni) {
                var form = document.getElementById('patient-form');
                form.action = 'paciente.php';
                form.method = 'POST';
                form.querySelector('input[name="action"]').value = 'read';
                form.submit();
            } else {
                alert('Por favor, ingrese un DNI para buscar.');
            }
        }
        <?php if ($mensaje): ?>
            alert("<?php echo addslashes($mensaje); ?>");
        <?php endif; ?>
    </script>
</body>
</html>
