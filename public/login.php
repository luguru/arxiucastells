<?php
session_start();
// Si ya hay una sesión activa, redirigir al gestor principal
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Gestor de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <header class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Gestor de Biblioteca</h1>
                <p class="text-gray-500 mt-2">Por favor, inicie sesión para continuar</p>
            </header>
            
            <form id="login-form">
                <div class="mb-4">
                    <label for="usuario" class="block text-sm font-medium text-gray-600 mb-1">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                <div class="mb-6">
                    <label for="contrasena" class="block text-sm font-medium text-gray-600 mb-1">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
                    <span class="block sm:inline">Usuario o contraseña incorrectos.</span>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                        Acceder
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/login.js"></script>
</body>
</html>
