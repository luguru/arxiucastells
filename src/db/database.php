<?php
// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "arxiucastells_";

// --- CONEXIÓN ---
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // En un entorno de producción, registrar este error en lugar de mostrarlo
    error_log("Error de conexión a la BBDD: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
    exit();
}
$conn->set_charset("utf8mb4");
?>