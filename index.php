<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
        <img src="public/img/Logo.png" alt="Logo" class="logo">
            <h2>Iniciar Sesión</h2>
            <form action="public/login_process.php" method="post">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
