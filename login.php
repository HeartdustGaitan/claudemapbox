<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];

    // Consulta a la tabla 'agentes' usando 'nombre' y 'celular' como credenciales
    $sql = "SELECT IdAgente, Nombre, Celular FROM agentes WHERE Nombre='$nombre' AND Celular='$telefono'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Guardar el IdAgentes en la sesión como user_id
        $_SESSION['user_id'] = $user['IdAgente'];

        // Redirigir a celulas.php
        header("Location: celulas.php");
        exit();
    } else {
        echo "Nombre o contraseña incorrectos.";
    }
}

$conn->close();
?>
