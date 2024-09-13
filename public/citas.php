<?php
include('../includes/db.php');

$nombre_paciente = '';
$dni_paciente = '';
$options = '';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['buscar-paciente'])) {
        // Obtener y sanitizar DNI del paciente
        $dni_paciente = htmlspecialchars($_POST['dni-paciente']);
        
        // Preparar la consulta SQL para obtener datos del paciente
        $stmt = $conn->prepare("SELECT nombre FROM pacientes WHERE dni = ?");
        $stmt->bind_param("s", $dni_paciente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Obtener los datos del paciente
            $row = $result->fetch_assoc();
            $nombre_paciente = $row['nombre'];
        } else {
            $nombre_paciente = "No se encontró paciente con el DNI ingresado.";
        }
        
        $stmt->close();
    } elseif (isset($_POST['registrar-cita'])) {
        // Obtener y sanitizar datos del formulario
        $dni_paciente = htmlspecialchars($_POST['dni-paciente']);
        $fecha_cita = htmlspecialchars($_POST['fecha-cita']);
        $hora_cita = htmlspecialchars($_POST['hora-cita']);
        $dni_psicologo = htmlspecialchars($_POST['dni-psicologo']);
        $notas = htmlspecialchars($_POST['notas']);

        // Calcular la hora de finalización sumando 45 minutos
        $hora_inicio = new DateTime($hora_cita);
        $hora_inicio->add(new DateInterval('PT45M'));
        $hora_finalizacion = $hora_inicio->format('H:i:s');

        // Preparar la consulta SQL para insertar una nueva cita
        $stmt = $conn->prepare("INSERT INTO citas (dni_paciente, fecha_cita, hora_cita, hora_finalizacion, dni_psicologo, estado, notas) VALUES (?, ?, ?, ?, ?, 'programada', ?)");
        $stmt->bind_param("ssssss", $dni_paciente, $fecha_cita, $hora_cita, $hora_finalizacion, $dni_psicologo, $notas);

        if ($stmt->execute()) {
            $mensaje = "Cita registrada exitosamente.";
        } else {
            $mensaje = "Error al registrar la cita: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Obtener psicólogos para el select
$stmt = $conn->prepare("SELECT dni, nombre FROM usuarios WHERE rol = 'psicologo'");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $options .= "<option value=\"" . htmlspecialchars($row['dni']) . "\">" . htmlspecialchars($row['nombre']) . "</option>";
}

$stmt->close();

// Función para obtener citas programadas
if (isset($_GET['action']) && $_GET['action'] == 'obtenerCitasProgramadas') {
    $stmt = $conn->prepare('
        SELECT c.fecha_cita, c.hora_cita, c.hora_finalizacion, u.nombre as nombre_psicologo, p.nombre as nombre_paciente 
        FROM citas c 
        JOIN usuarios u ON c.dni_psicologo = u.dni 
        JOIN pacientes p ON c.dni_paciente = p.dni
    ');
    $stmt->execute();
    $result = $stmt->get_result();
    $citas = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($citas);
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita</title>
    <link rel="stylesheet" href="css/citas.css?v=1.6">
    <script>
        // Función para mostrar un mensaje de alerta
        function mostrarMensaje(mensaje) {
            alert(mensaje);
        }

        // Función para cargar las citas programadas
        function cargarCitas() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'citas.php?action=obtenerCitasProgramadas', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const citas = JSON.parse(this.responseText);
                    const tbody = document.querySelector('#tabla-citas tbody');
                    tbody.innerHTML = '';

                    citas.forEach(cita => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${cita.nombre_psicologo}</td>
                            <td>${cita.nombre_paciente}</td>
                            <td>${cita.fecha_cita}</td>
                            <td>${cita.hora_cita}</td>
                            <td>${cita.hora_finalizacion}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            };
            xhr.send();
        }

        // Mostrar mensaje si existe
        window.onload = function() {
            <?php if ($mensaje): ?>
                mostrarMensaje("<?php echo $mensaje; ?>");
            <?php endif; ?>
            cargarCitas();
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Enlaces de navegación -->
            <a href="paciente.php"><img src="img/register_patient.png" alt="Registrar pacientes" class="icon">Registrar pacientes</a>
            <a href="citas.php"><img src="img/register_appointment.png" alt="Registrar cita" class="icon">Registrar cita</a>
            <a href="historiaclinica.php"><img src="img/clinical_history.png" alt="Historia clínica" class="icon">Historia clínica</a>
            <a href="pruebas.php"><img src="img/tests_done.png" alt="Pruebas realizadas" class="icon">Pruebas realizadas</a>
            <a href="reportecitas.php"><img src="img/appointment_report.png" alt="Reporte de citas" class="icon">Reporte de citas</a>
            <a href="usuarios.php"><img src="img/register_user.png" alt="Registrar usuarios" class="icon">Registrar usuarios</a>
            <img src="img/Logo.png" alt="Logo" class="logo">
        </div>
        <div class="main-content">
            <!-- Botón de cierre de sesión -->
            <button class="logout-button" onclick="location.href='logout.php'">Cerrar Sesión</button>
            <div class="form-container">
                <!-- Formulario de registro de cita -->
                <form id="citas-form" method="post" action="citas.php">
                    <h2>Registrar Cita</h2>
                    <label for="dni-paciente">DNI del Paciente</label>
                    <input type="text" id="dni-paciente" name="dni-paciente" value="<?php echo htmlspecialchars($dni_paciente); ?>" required>
                    <button type="submit" name="buscar-paciente">Buscar</button>

                    <label for="nombre-paciente">Nombre del Paciente</label>
                    <input type="text" id="nombre-paciente" name="nombre-paciente" value="<?php echo htmlspecialchars($nombre_paciente); ?>" readonly>

                    <label for="fecha-cita">Fecha de la Cita</label>
                    <input type="date" id="fecha-cita" name="fecha-cita" >

                    <label for="hora-cita">Hora de la Cita</label>
                    <input type="time" id="hora-cita" name="hora-cita" >

                    <label for="dni-psicologo">Psicólogo</label>
                    <select id="dni-psicologo" name="dni-psicologo" >
                        <?php echo $options; ?>
                    </select>

                    <label for="notas">Notas</label>
                    <textarea id="notas" name="notas"></textarea>

                    <button type="submit" name="registrar-cita">Registrar</button>
                </form>
            </div>
            <!-- Contenedor para la tabla de citas programadas -->
            <div class="citas-programadas">
                <h2>Citas Programadas</h2>
                <table id="tabla-citas">
                    <thead>
                        <tr>
                            <th>Psicólogo</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Hora de Inicio</th>
                            <th>Hora de Finalización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las citas se cargarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
