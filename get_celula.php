<?php
require_once 'db_connection.php';
require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener las credenciales de Twilio
$twilioSid = $_ENV['TWILIO_SID'];
$twilioToken = $_ENV['TWILIO_TOKEN'];
$twilio = new Client($twilioSid, $twilioToken);

// Configurar para recibir JSON
header("Content-Type: application/json");

// Decodificar el JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

// Validar los datos recibidos
if (isset($data['IdAgente']) && isset($data['IdIncidencia']) && 
    filter_var($data['IdAgente'], FILTER_VALIDATE_INT) && 
    filter_var($data['IdIncidencia'], FILTER_VALIDATE_INT)) {

    $usuario_id = $data['IdAgente'];
    $emergency_id = $data['IdIncidencia'];
    
    // Consulta para obtener la célula del agente
    $stmt = $conn->prepare("SELECT IdCelula, Nombre, Apellidos FROM agentes WHERE IdAgente = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $fila = $resultado->fetch_assoc()) {
        $cel = $fila['IdCelula'];
        $agenteNA = $fila['Nombre'].' '.$fila['Apellidos'];

        // Actualizar los datos en la tabla incidencias
        $stmt_update_inc = $conn->prepare("UPDATE incidencias SET IdEstatusI = ?, IdCelula = ? WHERE IdIncidencias = ?");
        $status = 2;
        $stmt_update_inc->bind_param("iii", $status, $cel, $emergency_id);

        if ($stmt_update_inc->execute()) {
            // Insertar en la tabla seguimiento
            $stmt_seguimiento = $conn->prepare("INSERT INTO seguimiento (Descripcion, Tipo, MessageType, IdIncidencias, IdCanal, IdEstatusI, login) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $descripcion = "La Incidencia ha sido asignada al Agente $agenteNA";
            $tipo = 'I';
            $message_type = 'Texto';
            $id_canal = 5;
            $login = 'admin';

            $stmt_seguimiento->bind_param("sssiiis", $descripcion, $tipo, $message_type, $emergency_id, $id_canal, $status, $login);

            if ($stmt_seguimiento->execute()) {
                // Actualizar el campo IdIncidencias en la tabla agentes
                $stmt_update_agent = $conn->prepare("UPDATE agentes SET IdIncidencias = ? WHERE IdAgente = ?");
                $stmt_update_agent->bind_param("ii", $emergency_id, $usuario_id);

                if ($stmt_update_agent->execute()) {
                    // Obtener Latitud y Longitud de la incidencia
                    $stmt_loc = $conn->prepare("SELECT Latitud, Longitud FROM incidencias WHERE IdIncidencias = ?");
                    $stmt_loc->bind_param("i", $emergency_id);
                    $stmt_loc->execute();
                    $result_loc = $stmt_loc->get_result();

                    if ($result_loc && $loc = $result_loc->fetch_assoc()) {
                        $latitud = $loc['Latitud'];
                        $longitud = $loc['Longitud'];

                        // Obtener el número de celular del agente
                        $stmt_phone = $conn->prepare("SELECT Celular FROM agentes WHERE IdAgente = ?");
                        $stmt_phone->bind_param("i", $usuario_id);
                        $stmt_phone->execute();
                        $result_phone = $stmt_phone->get_result();

                        if ($result_phone && $phone_data = $result_phone->fetch_assoc()) {
                            $celular = "+521" . $phone_data['Celular'];

                            // Crear el mensaje de WhatsApp con las coordenadas
                            $message_body = "Se le ha asignado una incidencia. Puede ver la ubicación en el siguiente enlace: https://maps.google.com/?q={$latitud},{$longitud}";

                            // Enviar el mensaje de WhatsApp
                            try {
                                $message = $twilio->messages->create(
                                    "whatsapp:$celular",
                                    [
                                        "from" => "whatsapp:+5213271092743",
                                        "body" => $message_body
                                    ]
                                );
                                echo json_encode(['success' => true, 'message' => 'Actualización, seguimiento y mensaje enviados correctamente']);
                            } catch (Exception $e) {
                                echo json_encode(['success' => false, 'error' => 'Error al enviar el mensaje de WhatsApp: ' . $e->getMessage()]);
                            }
                        } else {
                            echo json_encode(['success' => false, 'error' => 'Número de celular no encontrado']);
                        }
                        $stmt_phone->close();
                    } else {
                        echo json_encode(['success' => false, 'error' => 'No se encontró la ubicación en incidencias.']);
                    }
                    $stmt_loc->close();
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al actualizar el IdIncidencias en agentes: ' . $conn->error]);
                }
                $stmt_update_agent->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al insertar en seguimiento: ' . $conn->error]);
            }
            $stmt_seguimiento->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar los datos en incidencias: ' . $conn->error]);
        }
        $stmt_update_inc->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Agente no encontrado.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Datos insuficientes o inválidos']);
}

// Cerrar la conexión
$conn->close();
?>
