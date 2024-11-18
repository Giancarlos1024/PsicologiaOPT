<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'buscar') {
            $dni_paciente = $_POST['dni_paciente'];
            $sql = "SELECT * FROM pruebas_aplicadas WHERE dni_paciente = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $dni_paciente);
            $stmt->execute();
            $result = $stmt->get_result();

            $pruebas = array();
            while ($row = $result->fetch_assoc()) {
                $pruebas[] = $row;
            }
            echo json_encode($pruebas);
            exit;
        } elseif ($action == 'registrar' || $action == 'editar') {
            $id_prueba = isset($_POST['id_prueba']) ? $_POST['id_prueba'] : null;
            $dni_paciente = $_POST['dni_paciente'];
            $nombre_prueba = $_POST['nombre_prueba'];
            $resultado = $_POST['resultado'];
            $fecha_cita = $_POST['fecha_cita'];
            $hora_cita = $_POST['hora_cita'];

            // Manejo de archivo
            $foto_prueba = '';
            if (isset($_FILES['foto_prueba']) && $_FILES['foto_prueba']['error'] == 0) {
                $foto_prueba = 'uploads/' . basename($_FILES['foto_prueba']['name']);
                move_uploaded_file($_FILES['foto_prueba']['tmp_name'], $foto_prueba);
            }

            if ($action == 'registrar') {
                $sql = "INSERT INTO pruebas_aplicadas (dni_paciente, nombre_prueba, resultado, foto_prueba, fecha_cita, hora_cita) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $dni_paciente, $nombre_prueba, $resultado, $foto_prueba, $fecha_cita, $hora_cita);
            } elseif ($action == 'editar') {
                if ($foto_prueba) {
                    $sql = "UPDATE pruebas_aplicadas SET dni_paciente = ?, nombre_prueba = ?, resultado = ?, foto_prueba = ?, fecha_cita = ?, hora_cita = ? WHERE id_prueba = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssi", $dni_paciente, $nombre_prueba, $resultado, $foto_prueba, $fecha_cita, $hora_cita, $id_prueba);
                } else {
                    $sql = "UPDATE pruebas_aplicadas SET dni_paciente = ?, nombre_prueba = ?, resultado = ?, fecha_cita = ?, hora_cita = ? WHERE id_prueba = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $dni_paciente, $nombre_prueba, $resultado, $fecha_cita, $hora_cita, $id_prueba);
                }
            }

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error en la operación.']);
            }
            exit;
        } elseif ($action == 'eliminar') {
            $id_prueba = $_POST['id_prueba'];
            $sql = "DELETE FROM pruebas_aplicadas WHERE id_prueba = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_prueba);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar.']);
            }
            exit;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Prueba</title>
    <link rel="stylesheet" href="css/pruebas.css?v=2.6">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Enlaces de la barra lateral -->
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
                <form id="prueba-form" enctype="multipart/form-data">
                    <h2>Registrar Prueba</h2>
                    <label for="dni_paciente">DNI del Paciente</label>
                    <input type="text" id="dni_paciente" name="dni_paciente" required>
                    <button type="button" id="buscar_paciente_btn">Buscar</button>

                    <label for="fecha_cita">Fecha de la Cita</label>
                    <input type="date" id="fecha_cita" name="fecha_cita" required>

                    <label for="hora_cita">Hora de la Cita</label>
                    <input type="time" id="hora_cita" name="hora_cita" required>

                    <label for="nombre_prueba">Nombre de la Prueba</label>
                    <input type="text" id="nombre_prueba" name="nombre_prueba" required>

                    <label for="resultado">Resultado</label>
                    <input type="text" id="resultado" name="resultado" required>

                    <label for="foto_prueba">Archivo de la Prueba</label>
                    <input type="file" id="foto_prueba" name="foto_prueba" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*">

                    <input type="hidden" id="id_prueba" name="id_prueba">

                    <div class="form-buttons">
                        <button type="button" id="registrar-btn" onclick="registerPrueba()">Registrar</button>
                        <button type="button" id="actualizar-btn" onclick="updatePrueba()">Actualizar</button>
                    </div>
                </form>
            </div>
            <h2 class="titulo-tablaPruebas">Pruebas Asociadas</h2>
            <table id="pruebas-paciente-table" class="tabla-personalizada">
                <thead>
                    <tr>
                        <th>Nombre de la Prueba</th>
                        <th>Resultado</th>
                        <th>Fecha de la Cita</th>
                        <th>Hora de la Cita</th>
                        <th>Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se cargarán los datos de las pruebas -->
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById('buscar_paciente_btn').addEventListener('click', function() {
            var dni = document.getElementById('dni_paciente').value;
            if (dni) {
                var formData = new FormData();
                formData.append('dni_paciente', dni);
                formData.append('action', 'buscar');

                fetch('pruebas.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    var tableBody = document.querySelector('#pruebas-paciente-table tbody');
                    tableBody.innerHTML = '';

                    data.forEach(prueba => {
                        var row = document.createElement('tr');

                        var nombrePruebaCell = document.createElement('td');
                        nombrePruebaCell.textContent = prueba.nombre_prueba;
                        row.appendChild(nombrePruebaCell);

                        var resultadoCell = document.createElement('td');
                        resultadoCell.textContent = prueba.resultado;
                        row.appendChild(resultadoCell);

                        var fechaCitaCell = document.createElement('td');
                        fechaCitaCell.textContent = prueba.fecha_cita;
                        row.appendChild(fechaCitaCell);

                        var horaCitaCell = document.createElement('td');
                        horaCitaCell.textContent = prueba.hora_cita;
                        row.appendChild(horaCitaCell);

                        var archivoCell = document.createElement('td');
                        if (prueba.foto_prueba) {
                            var archivoLink = document.createElement('a');
                            archivoLink.href = prueba.foto_prueba;
                            archivoLink.textContent = 'Ver Archivo';
                            archivoLink.target = '_blank';  // Abrir en una nueva pestaña
                            archivoCell.appendChild(archivoLink);
                        } else {
                            archivoCell.textContent = 'No disponible';
                        }
                        row.appendChild(archivoCell);

                        var accionesCell = document.createElement('td');
                        var editarBtn = document.createElement('button');
                        editarBtn.textContent = 'Editar';
                        editarBtn.addEventListener('click', function() {
                            editPrueba(prueba);
                        });
                        accionesCell.appendChild(editarBtn);

                        var eliminarBtn = document.createElement('button');
                        eliminarBtn.textContent = 'Eliminar';
                        eliminarBtn.addEventListener('click', function() {
                            deletePrueba(prueba.id_prueba);
                        });
                        accionesCell.appendChild(eliminarBtn);

                        row.appendChild(accionesCell);

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error al buscar el paciente:', error);
                });
            } else {
                alert('Por favor, ingrese un DNI.');
            }
        });

        function registerPrueba() {
            var form = document.getElementById('prueba-form');
            var formData = new FormData(form);
            formData.append('action', 'registrar');

            fetch('pruebas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Prueba registrada exitosamente.');
                    form.reset();
                    document.getElementById('buscar_paciente_btn').click(); // Recargar la tabla
                } else {
                    alert('Error al registrar la prueba.');
                }
            })
            .catch(error => {
                console.error('Error al registrar la prueba:', error);
            });
        }

        function updatePrueba() {
            var form = document.getElementById('prueba-form');
            var formData = new FormData(form);
            formData.append('action', 'editar');

            fetch('pruebas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Prueba actualizada exitosamente.');
                    form.reset();
                    document.getElementById('registrar-btn').style.display = 'inline';
                    document.getElementById('actualizar-btn').style.display = 'none';
                    document.getElementById('buscar_paciente_btn').click(); // Recargar la tabla
                } else {
                    alert('Error al actualizar la prueba.');
                }
            })
            .catch(error => {
                console.error('Error al actualizar la prueba:', error);
            });
        }

        function deletePrueba(id_prueba) {
            if (confirm('¿Está seguro de que desea eliminar esta prueba?')) {
                var formData = new FormData();
                formData.append('id_prueba', id_prueba);
                formData.append('action', 'eliminar');

                fetch('pruebas.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Prueba eliminada exitosamente.');
                        document.getElementById('buscar_paciente_btn').click(); // Recargar la tabla
                    } else {
                        alert('Error al eliminar la prueba.');
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar la prueba:', error);
                });
            }
        }

        function editPrueba(prueba) {
            document.getElementById('id_prueba').value = prueba.id_prueba;
            document.getElementById('dni_paciente').value = prueba.dni_paciente;
            document.getElementById('nombre_prueba').value = prueba.nombre_prueba;
            document.getElementById('resultado').value = prueba.resultado;
            document.getElementById('fecha_cita').value = prueba.fecha_cita;
            document.getElementById('hora_cita').value = prueba.hora_cita;
        
        }
    </script>
</body>
</html>
