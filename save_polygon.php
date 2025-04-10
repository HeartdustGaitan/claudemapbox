<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include 'db_connection.php'; // Include your database connection

$user_id = $_SESSION['user_id'];
$coordinates = $_POST['coordinates'];

$sql = "INSERT INTO polygons (user_id, coordinates) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $coordinates);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving polygon']);
}

$stmt->close();
$conn->close();
?>
