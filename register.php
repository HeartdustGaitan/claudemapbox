<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];

    $sql = "INSERT INTO s_users (nombre, pswd) VALUES ('$nombre', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso!";
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="register-form" method="POST" action="register.php">
                <h2>Registro</h2>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <select name="tipo" required>
                    <option value="celula">Celula</option>
                    <option value="administrador">Administrador</option>
                </select>
                <button type="submit">Registrarse</button>
                <p>¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a></p>
            </form>
        </div>
    </div>
</body>
</html>
