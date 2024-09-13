<?php
include('../includes/db.php');

// Variables para manejar los mensajes
$message = "";
$clear_form = false;

// CREAR USUARIO
if (isset($_POST['register-btn'])) {
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (dni, nombre, apellidos, email, contrasena, rol) VALUES ('$dni', '$nombre', '$apellidos', '$email', '$password', '$rol')";

    if ($conn->query($sql) === TRUE) {
        $message = "Usuario registrado exitosamente";
        $clear_form = true;
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// LEER USUARIO
if (isset($_POST['search-btn'])) {
    $dni = $_POST['dni'];

    $sql = "SELECT * FROM usuarios WHERE dni='$dni'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Rellenar el formulario con los datos del usuario
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $apellidos = $row['apellidos'];
        $email = $row['email'];
        $password = $row['contrasena'];
        $rol = $row['rol'];
    } else {
        $message = "Usuario no encontrado";
    }
}

// ACTUALIZAR USUARIO
if (isset($_POST['edit-btn'])) {
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', email='$email', contrasena='$password', rol='$rol' WHERE dni='$dni'";

    if ($conn->query($sql) === TRUE) {
        $message = "Usuario actualizado exitosamente";
        $clear_form = true;
    } else {
        $message = "Error al actualizar usuario: " . $conn->error;
    }
}

// ELIMINAR USUARIO
if (isset($_POST['delete-btn'])) {
    $dni = $_POST['dni'];

    $sql = "DELETE FROM usuarios WHERE dni='$dni'";

    if ($conn->query($sql) === TRUE) {
        $message = "Usuario eliminado exitosamente";
        $clear_form = true;
    } else {
        $message = "Error al eliminar usuario: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="stylesheet" href="css/usuarios.css?v=1.0">
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
            <button class="logout-button" onclick="location.href='logout.php'">Cerrar Sesión</button>
            <div class="form-container">
                <form id="user-form" method="post" action="">
                    <h2>Datos de Usuario</h2>
                    <label for="dni">DNI:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo isset($dni) ? $dni : ''; ?>">
                    <button type="submit" name="search-btn">Buscar</button>

                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>">

                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo isset($apellidos) ? $apellidos : ''; ?>">

                    <label for="email">Correo:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" value="<?php echo isset($password) ? $password : ''; ?>">

                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol">
                        <option value="admin" <?php if (isset($rol) && $rol == "admin") echo "selected"; ?>>Admin</option>
                        <option value="psicologo" <?php if (isset($rol) && $rol == "psicologo") echo "selected"; ?>>Psicologo</option>
                    </select>

                    <button type="submit" name="register-btn">Registrar</button>
                    <button type="submit" name="edit-btn">Editar</button>
                    <button type="submit" name="delete-btn">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mostrar alerta y limpiar el formulario si $message no está vacío -->
    <?php if (!empty($message)): ?>
        <script>
            // Mostrar mensaje de alerta
            alert("<?php echo $message; ?>");

            // Limpiar el formulario si $clear_form es true
            <?php if ($clear_form): ?>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('user-form').reset();
                });
            <?php endif; ?>
        </script>
    <?php endif; ?>
</body>
</html>
