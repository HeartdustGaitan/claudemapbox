<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener las credenciales de Twilio
$twilioSid    = $_ENV['TWILIO_SID'];
$twilioToken  = $_ENV['TWILIO_TOKEN'];

// Inicializar el cliente de Twilio
$twilio = new Client($twilioSid, $twilioToken);

// Obtener los números de teléfono seleccionados desde el formulario
$selectedPhones = $_POST['cel_promovido'];

// Agregar la extensión "+521" al principio de cada número seleccionado
$formattedPhones = array_map(function($phone) {
    return "+521" . $phone;
}, $selectedPhones);

// Obtener el mensaje manual ingresado desde el formulario
$mensajeManual = $_POST['mensaje_manual'];

// Verificar si se proporcionó un mensaje manual y usarlo si está presente
if (!empty($mensajeManual)) {
    $selectedMessage = $mensajeManual;
} else {
    // Si no se proporcionó un mensaje manual, usar el mensaje seleccionado desde el formulario
    $selectedMessage = $_POST['mensaje'];
}

// Variable para almacenar el resultado del envío
$mensajeEnviado = true;

// Iterar sobre los números seleccionados y enviar un mensaje a cada uno
foreach ($formattedPhones as $phone) {
    // Crear y enviar el mensaje de WhatsApp
    try {
        $message = $twilio->messages
                         ->create(
                             "whatsapp:$phone", // Usar el número seleccionado
                             array(
                                  "from" => "whatsapp:+5213112703314",
                                  "body" => $selectedMessage // Usar el mensaje seleccionado
                             )
                         );
    } catch (Exception $e) {
        // Si se produce una excepción, mostrar el mensaje de error (puedes modificar esto según tus necesidades)
        $mensajeEnviado = false;
        echo "Error al enviar el mensaje a $phone: " . $e->getMessage();
    }
}

// Si los mensajes se enviaron correctamente, mostrar una alerta de éxito
if ($mensajeEnviado) {
    echo "<script>alert('Mensajes enviados con éxito');</script>";
}

// Redireccionar a la página anterior o a donde sea necesario después de enviar los mensajes
header("Location: mensajes.php"); // Cambia seg.php por la página a la que quieras redireccionar
exit();
