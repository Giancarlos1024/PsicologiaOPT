<?php
include('../includes/db.php');

function obtenerCitasPorDNI($conn, $dni) {
    $sql = "SELECT fecha_cita, hora_cita FROM citas WHERE dni_paciente = '$dni'";
    $result = $conn->query($sql);
    $citas = [];
    while ($row = $result->fetch_assoc()) {
        $citas[] = $row;
    }
    return $citas;
}

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
    
    // Asegúrate de que las citas también se actualicen según el nuevo DNI
    $citas = obtenerCitasPorDNI($conn, $dni);

    // Limpiar campos de datos del paciente previo si es necesario
    if ($paciente) {
        $nombre = $paciente['nombre'] . ' ' . $paciente['apellidos'];
    } else {
        // Si no hay paciente, limpia los valores
        $nombre = '';
    }

    // Opcionalmente, también puedes limpiar otros valores en el formulario si el paciente no existe
    $fecha_cita = '';
    $hora_cita = '';
    $motivo_consulta = '';
    $antecedentes = '';
    $diagnostico = '';
    $tratamiento = '';
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
            <a href="pacientes.php"><img src="img/appointment_report.png" alt="Reporte de pacientes" class="icon">Reporte de pacientes</a>
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
                    <button type="submit" name="buscar_paciente">Buscar</button></br>

                    <?php if ($paciente): ?>
                        <input type="hidden" id="id_historia" name="id_historia">
                        <label for="nombre_paciente">Nombre</label>
                        <input type="text" id="nombre_paciente" name="nombre_paciente" value="<?php echo htmlspecialchars($nombre); ?>" readonly>

                        <label for="fecha_cita">Fecha de Cita</label>
                        <select id="fecha_cita" name="fecha_cita">
                            <?php foreach ($citas as $cita): ?>
                                <option value="<?php echo $cita['fecha_cita']; ?>" <?php echo ($fecha_cita == $cita['fecha_cita']) ? 'selected' : ''; ?>><?php echo $cita['fecha_cita']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="hora_cita">Hora de Cita</label>
                        <select id="hora_cita" name="hora_cita">
                            <?php foreach ($citas as $cita): ?>
                                <option value="<?php echo $cita['hora_cita']; ?>" <?php echo ($hora_cita == $cita['hora_cita']) ? 'selected' : ''; ?>><?php echo $cita['hora_cita']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="motivo_consulta">Motivo de consulta</label>
                        <textarea id="motivo_consulta" name="motivo_consulta"><?php echo htmlspecialchars($motivo_consulta); ?></textarea>

                        <label for="antecedentes">Antecedentes</label>
                        <textarea id="antecedentes" name="antecedentes"><?php echo htmlspecialchars($antecedentes); ?></textarea>

                        <label for="diagnostico_presuntivo">Diagnóstico presuntivo</label>
                        <textarea id="diagnostico_presuntivo" name="diagnostico_presuntivo"><?php echo htmlspecialchars($diagnostico); ?></textarea>

                        <label for="tratamiento">Tratamiento</label>
                        <textarea id="tratamiento" name="tratamiento"><?php echo htmlspecialchars($tratamiento); ?></textarea>

                        <label for="pdf_datos">Subir PDF</label>
                        <input type="file" id="pdf_datos" name="pdf_datos">

                        <button type="submit" name="registrar_historia">Registrar Historia</button>
                    <?php endif; ?>
                </form>

            </div>
            <div class="contenedorTablaHistoria">
                <?php if ($paciente): ?>
                    <h2>Historias Clínicas</h2>
                    <div class="tabla-wrapper"> 
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
                                        <td>
                                            <?php
                                                $motivo = htmlspecialchars($historia['motivo_consulta']);
                                                if (strlen($motivo) > 20) {
                                                    echo "<div class='campo-largo' id='motivo-{$historia['id_historia']}'>" . substr($motivo, 0, 20) . "...</div>";
                                                    echo "<a href='javascript:void(0);' onclick='mostrarTexto(\"motivo-{$historia['id_historia']}\", \"{$motivo}\")'>Más</a>";
                                                } else {
                                                    echo $motivo;
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                                $antecedentes = htmlspecialchars($historia['antecedentes']);
                                                if (strlen($antecedentes) > 20) {
                                                    echo "<div class='campo-largo' id='antecedentes-{$historia['id_historia']}'>" . substr($antecedentes, 0, 20) . "...</div>";
                                                    echo "<a href='javascript:void(0);' onclick='mostrarTexto(\"antecedentes-{$historia['id_historia']}\", \"{$antecedentes}\")'>Más</a>";
                                                } else {
                                                    echo $antecedentes;
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                                $diagnostico = htmlspecialchars($historia['diagnostico_probable']);
                                                if (strlen($diagnostico) > 20) {
                                                    echo "<div class='campo-largo' id='diagnostico-{$historia['id_historia']}'>" . substr($diagnostico, 0, 20) . "...</div>";
                                                    echo "<a href='javascript:void(0);' onclick='mostrarTexto(\"diagnostico-{$historia['id_historia']}\", \"{$diagnostico}\")'>Más</a>";
                                                } else {
                                                    echo $diagnostico;
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                                $tratamiento = htmlspecialchars($historia['tratamiento']);
                                                if (strlen($tratamiento) > 20) {
                                                    echo "<div class='campo-largo' id='tratamiento-{$historia['id_historia']}'>" . substr($tratamiento, 0, 20) . "...</div>";
                                                    echo "<a href='javascript:void(0);' onclick='mostrarTexto(\"tratamiento-{$historia['id_historia']}\", \"{$tratamiento}\")'>Más</a>";
                                                } else {
                                                    echo $tratamiento;
                                                }
                                            ?>
                                        </td>

                                        <td><?php echo htmlspecialchars($historia['fecha_cita']); ?></td>
                                        <td><?php echo htmlspecialchars($historia['hora_cita']); ?></td>
                                        <td>
                                            <?php if ($historia['pdf_datos']): ?>
                                                <a href="uploads/<?php echo htmlspecialchars($historia['pdf_datos']); ?>" target="_blank">Ver PDF</a>
                                            <?php endif; ?>
                                        </td>
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
                    </div>
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

        function actualizarHoraCita() {
            const selectFechaCita = document.getElementById('fecha_cita');
            const inputHoraCita = document.getElementById('hora_cita');
            const seleccion = selectFechaCita.value;
            if (seleccion) {
                const [fecha, hora] = seleccion.split('|');
                inputHoraCita.value = hora;
            } else {
                inputHoraCita.value = '';
            }
        }

        function mostrarTexto(id, textoCompleto) {
            var campo = document.getElementById(id);
            var enlace = campo.nextElementSibling;
            if (campo.innerText === textoCompleto.substr(0, 20) + "...") {
                campo.innerText = textoCompleto;
                enlace.innerText = "Menos";
            } else {
                campo.innerText = textoCompleto.substr(0, 20) + "...";
                enlace.innerText = "Más";
            }
        }

       



    </script>
</body>
</html>

<?php
// Cerrar la conexión al final del archivo
$conn->close();
?>
