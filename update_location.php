<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = $_POST['Latitud'];
    $lng = $_POST['Longitud'];
    $user_id = $_POST['user_id'];

    // Verificar si ya existe una entrada para este usuario en la tabla agentes
    $query = "SELECT * FROM agentes WHERE IdAgente = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si el usuario ya tiene coordenadas, solo se actualizan
        $query = "UPDATE agentes SET Latitud = ?, Longitud = ? WHERE IdAgente = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $lat, $lng, $user_id);
    } else {
        // Si no existe una entrada, se inserta una nueva
        $query = "INSERT INTO agentes (IdAgente, Latitud, Longitud) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $user_id, $lat, $lng);
    }

    if ($stmt->execute()) {
        echo "Ubicación insertada/actualizada correctamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método de solicitud no permitido.";
}
?>
