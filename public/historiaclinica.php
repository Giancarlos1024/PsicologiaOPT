<?php
include('../includes/db.php');

function obtenerPacientePorDNI($conn, $dni) {
    $sql = "SELECT * FROM pacientes WHERE dni = '$dni'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function obtenerHistoriasClinicasPorDNI($conn, $dni) {
    $sql = "SELECT * FROM historias_clinicas WHERE dni_paciente = '$dni'";
    $result = $conn->query($sql);
    $historias = [];
    while ($row = $result->fetch_assoc()) {
        $historias[] = $row;
    }
    return $historias;
}

function registrarHistoria($conn, $dni, $motivo, $antecedentes, $diagnostico, $tratamiento, $fecha, $hora, $pdf) {
    $sql = "INSERT INTO historias_clinicas (dni_paciente, motivo_consulta, antecedentes, diagnostico_probable, tratamiento, fecha_cita, hora_cita, pdf_datos)
            VALUES ('$dni', '$motivo', '$antecedentes', '$diagnostico', '$tratamiento', '$fecha', '$hora', '$pdf')";
    return $conn->query($sql);
}

function actualizarHistoria($conn, $id, $motivo, $antecedentes, $diagnostico, $tratamiento, $hora, $pdf) {
    $sql = "UPDATE historias_clinicas SET motivo_consulta='$motivo', antecedentes='$antecedentes', diagnostico_probable='$diagnostico', tratamiento='$tratamiento', hora_cita='$hora', pdf_datos='$pdf' WHERE id_historia='$id'";
    return $conn->query($sql);
}

function eliminarHistoria($conn, $id) {
    $sql = "DELETE FROM historias_clinicas WHERE id_historia='$id'";
    return $conn->query($sql);
}

$paciente = null;
$historias = [];
$dni = '';

if (isset($_POST['buscar_paciente'])) {
    $dni = $_POST['dni_paciente'];
    $paciente = obtenerPacientePorDNI($conn, $dni);
    $historias = obtenerHistoriasClinicasPorDNI($conn, $dni);
}

if (isset($_POST['registrar_historia'])) {
    $dni = $_POST['dni_paciente'];
    $motivo = $_POST['motivo_consulta'];
    $antecedentes = $_POST['antecedentes'];
    $diagnostico = $_POST['diagnostico_presuntivo'];
    $tratamiento = $_POST['tratamiento'];
    $fecha = $_POST['fecha_cita'];
    $hora = $_POST['hora_cita'];
    $pdf = ''; // Aquí deberías manejar la subida del archivo PDF
    if (isset($_FILES['pdf_datos']) && $_FILES['pdf_datos']['error'] == UPLOAD_ERR_OK) {
        $pdf = $_FILES['pdf_datos']['name'];
        move_uploaded_file($_FILES['pdf_datos']['tmp_name'], 'uploads/' . $pdf);
    }
    registrarHistoria($conn, $dni, $motivo, $antecedentes, $diagnostico, $tratamiento, $fecha, $hora, $pdf);
    $paciente = obtenerPacientePorDNI($conn, $dni);
    $historias = obtenerHistoriasClinicasPorDNI($conn, $dni);
}

if (isset($_POST['actualizar_historia'])) {
    $id = $_POST['id_historia'];
    $motivo = $_POST['motivo_consulta'];
    $antecedentes = $_POST['antecedentes'];
    $diagnostico = $_POST['diagnostico_presuntivo'];
    $tratamiento = $_POST['tratamiento'];
    $hora = $_POST['hora_cita'];
    $pdf = ''; // Aquí deberías manejar la subida del archivo PDF
    if (isset($_FILES['pdf_datos']) && $_FILES['pdf_datos']['error'] == UPLOAD_ERR_OK) {
        $pdf = $_FILES['pdf_datos']['name'];
        move_uploaded_file($_FILES['pdf_datos']['tmp_name'], 'uploads/' . $pdf);
    }
    actualizarHistoria($conn, $id, $motivo, $antecedentes, $diagnostico, $tratamiento, $hora, $pdf);
    $dni = $_POST['dni_paciente'];
    $paciente = obtenerPacientePorDNI($conn, $dni);
    $historias = obtenerHistoriasClinicasPorDNI($conn, $dni);
}

if (isset($_POST['eliminar_historia'])) {
    $id = $_POST['id_historia'];
    eliminarHistoria($conn, $id);
    $dni = $_POST['dni_paciente'];
    $paciente = obtenerPacientePorDNI($conn, $dni);
    $historias = obtenerHistoriasClinicasPorDNI($conn, $dni);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Historia Clínica</title>
    <link rel="stylesheet" href="css/historiaclinica.css?v=2.6">
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
            <button class="logout-button" onclick="location.href='logout.php'">Cerrar Sesión</button>
            <div class="form-container">
                <form id="historia_clinica_form" method="POST" enctype="multipart/form-data">
                    <h2>Datos de consulta</h2>
                    <label for="dni_paciente">DNI del Paciente</label>
                    <input type="text" id="dni_paciente" name="dni_paciente" value="<?php echo htmlspecialchars($dni); ?>" required>
                    <button type="submit" name="buscar_paciente">Buscar</button>

                    <?php if ($paciente): ?>
                        <input type="hidden" id="id_historia" name="id_historia">
                        <label for="nombre_paciente">Nombre</label>
                        <input type="text" id="nombre_paciente" name="nombre_paciente" value="<?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']); ?>" readonly>

                        <label for="fecha_cita">Fecha de Cita</label>
                        <input type="date" id="fecha_cita" name="fecha_cita" required>

                        <label for="hora_cita">Hora de Cita</label>
                        <input type="time" id="hora_cita" name="hora_cita" required>

                        <label for="motivo_consulta">Motivo de consulta</label>
                        <textarea id="motivo_consulta" name="motivo_consulta" required></textarea>

                        <label for="antecedentes">Antecedentes</label>
                        <textarea id="antecedentes" name="antecedentes" required></textarea>

                        <label for="diagnostico_presuntivo">Diagnóstico Presuntivo</label>
                        <textarea id="diagnostico_presuntivo" name="diagnostico_presuntivo" required></textarea>

                        <label for="tratamiento">Tratamiento</label>
                        <textarea id="tratamiento" name="tratamiento" required></textarea>

                        <label for="pdf_datos">PDF</label>
                        <input type="file" id="pdf_datos" name="pdf_datos" accept="application/pdf">

                        <div class="form-buttons">
                            <button type="submit" name="registrar_historia">Registrar</button>
                            <button type="submit" name="actualizar_historia">Actualizar</button>
                            
                        </div>
                    <?php endif; ?>
                </form>

                <?php if ($paciente): ?>
                    <h2>Historias Clínicas</h2>
                    <table class="tabla-personalizada">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Motivo</th>
                                <th>Antecedentes</th>
                                <th>Diagnóstico</th>
                                <th>Tratamiento</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>PDF</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historias as $historia): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($historia['id_historia']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['motivo_consulta']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['antecedentes']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['diagnostico_probable']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['tratamiento']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['fecha_cita']); ?></td>
                                    <td><?php echo htmlspecialchars($historia['hora_cita']); ?></td>
                                    <td><a href="uploads/<?php echo htmlspecialchars($historia['pdf_datos']); ?>" target="_blank">Ver PDF</a></td>
                                    <td>
                                        <button onclick="cargarHistoria(<?php echo htmlspecialchars($historia['id_historia']); ?>)">Editar</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_historia" value="<?php echo htmlspecialchars($historia['id_historia']); ?>">
                                            <input type="hidden" name="dni_paciente" value="<?php echo htmlspecialchars($dni); ?>">
                                            <button type="submit" name="eliminar_historia">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function cargarHistoria(id) {
            const form = document.getElementById('historia_clinica_form');
            const historia = <?php echo json_encode($historias); ?>.find(historia => historia.id_historia == id);
            if (historia) {
                form.id_historia.value = historia.id_historia;
                form.motivo_consulta.value = historia.motivo_consulta;
                form.antecedentes.value = historia.antecedentes;
                form.diagnostico_presuntivo.value = historia.diagnostico_probable;
                form.tratamiento.value = historia.tratamiento;
                form.fecha_cita.value = historia.fecha_cita;
                form.hora_cita.value = historia.hora_cita;
            }
        }
    </script>
</body>
</html>
