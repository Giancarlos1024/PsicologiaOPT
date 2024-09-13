<?php
// $servername = "sql312.infinityfree.com";
// $username = "if0_37236684";
// $password = "nXFg0vZ3GON4r";
// $dbname = "if0_37236684_fdf_psicologia";
$servername = "localhost:3306";
$username = "root";
$password = "123456";
$dbname = "fdf_psicologia";


// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
