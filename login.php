<?php
session_start();
include('db_connection.php');

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verificar si ya hay una sesión activa
if (isset($_SESSION['user_id'])) {
    // Redirigir a la página principal si ya está logueado
    header("Location: celulas.php");
    exit();
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y limpiar entradas
    $nombre = cleanInput($_POST['nombre']);
    $telefono = cleanInput($_POST['telefono']);
    
    if (empty($nombre) || empty($telefono)) {
        header("Location: index.php?error=empty");
        exit();
    }

    // Consulta preparada para evitar inyección SQL
    $sql = "SELECT IdAgente, Nombre, Celular FROM agentes WHERE Nombre=? AND Celular=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $telefono);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Guardar datos importantes en la sesión
        $_SESSION['user_id'] = $user['IdAgente'];
        $_SESSION['user_name'] = $user['Nombre'];
        $_SESSION['last_activity'] = time();
        
        // Registrar login exitoso
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $login_time = date('Y-m-d H:i:s');
        
        // Opcional: registrar el login en una tabla de la BD
        // $log_sql = "INSERT INTO login_logs (user_id, ip, user_agent, login_time) VALUES (?, ?, ?, ?)";
        // $log_stmt = $conn->prepare($log_sql);
        // $log_stmt->bind_param("isss", $user['IdAgente'], $ip, $user_agent, $login_time);
        // $log_stmt->execute();
        
        // Redirigir a la página principal
        header("Location: celulas.php");
        exit();
    } else {
        // Login fallido
        header("Location: index.php?error=invalid");
        exit();
    }
    
    $stmt->close();
} else {
    // Si alguien intenta acceder directamente a login.php, redirigir
    header("Location: index.php");
    exit();
}

$conn->close();
?>