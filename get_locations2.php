<?php
session_start();
header('Content-Type: application/json');
include 'db_connection.php';

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
}

// Obtener el valor de incidenciaId desde la sesión
$incidenciaId = isset($_SESSION['incidenciaId']) ? $_SESSION['incidenciaId'] : 10; 
$usuario_id = $_SESSION['user_id'];

//echo "<script>console.log('Incidencia ID2: ' + $incidenciaId);</script>";

// Consulta para obtener ubicaciones de células
$query_locations = "SELECT IdAgente AS usuario_id, Latitud, Longitud, Nombre, Celular
                     FROM agentes
                     WHERE  IdAgente = $usuario_id";
$result_locations = $conn->query($query_locations);

if (!$result_locations) {
    die(json_encode(['error' => 'Error en la consulta de ubicaciones: ' . $conn->error]));
}

$locations = array();

while ($row = $result_locations->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'celula',
        'usuario_id' => $row['usuario_id'],
        'lat' => $row['Latitud'],
        'lng' => $row['Longitud'],
        'nombre' => $row['Nombre'],
        'telefono' => $row['Celular']
    );
}

// Consulta para obtener emergencias
$query_emergencies = "SELECT IdIncidencias, Descripcion, Latitud, Longitud FROM incidencias WHERE IdEstatusI = 2";
$result_emergencies = $conn->query($query_emergencies);

if (!$result_emergencies) {
    die(json_encode(['error' => 'Error en la consulta de emergencias: ' . $conn->error]));
}

while ($row = $result_emergencies->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'emergency',
        'id' => $row['IdIncidencias'],
        'lat' => $row['Latitud'],
        'lng' => $row['Longitud'],
        'nombre' => $row['Descripcion'],
        'telefono' => '3112041132'
    );
}

// Consulta para obtener emergencias la ultima
$query_emergencies2 = "SELECT IdIncidencias, Descripcion, Latitud, Longitud FROM incidencias WHERE IdEstatusI = 1";
$result_emergencies2 = $conn->query($query_emergencies2);

if (!$result_emergencies2) {
    die(json_encode(['error' => 'Error en la consulta de emergencias: ' . $conn->error]));
}

while ($row = $result_emergencies2->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'emergency1',
        'id' => $row['IdIncidencias'],
        'lat' => $row['Latitud'],
        'lng' => $row['Longitud'],
        'nombre' => $row['Descripcion'],
        'telefono' => '3112041132'
    );
}

// Consulta para obtener la ultima incidencia
$query_emergencies3 = "SELECT IdIncidencias, Descripcion, Latitud, Longitud FROM incidencias WHERE IdIncidencias = $incidenciaId";
$result_emergencies3 = $conn->query($query_emergencies3);
//echo "<script>console.log('Query: ' + $query_emergencies3);</script>";

if (!$result_emergencies3) {
    die(json_encode(['error' => 'Error en la consulta de emergencias: ' . $conn->error]));
}

while ($row = $result_emergencies3->fetch_assoc()) {
    $locations[] = array(
        'tipo' => 'emergency2',
        'id' => $row['IdIncidencias'],
        'lat' => $row['Latitud'],
        'lng' => $row['Longitud'],
        'nombre' => $row['Descripcion'],
        'telefono' => '3112041132'
    );
}

echo json_encode($locations);
$conn->close();
?>
