<?php
session_start();
// Proteger la página. Si el usuario no ha iniciado sesión, redirigir a login.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestor de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body class="bg-gray-100 p-4 md:p-8">
    <div id="app-container" class="max-w-7xl mx-auto bg-white rounded-2xl shadow-lg p-6 md:p-8">
      <header class="mb-8 text-center relative">
        <h1 class="text-4xl font-bold text-gray-800">Gestor de Biblioteca</h1>
        <p class="text-gray-500 mt-2">Administra y busca en tu catálogo de libros de forma sencilla.</p>
         <div class="absolute top-0 right-0 flex items-center space-x-2">
            <span class="text-sm text-gray-600 font-medium">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="bg-red-500 text-white py-1 px-3 rounded-lg text-sm font-semibold hover:bg-red-600 transition shadow-sm">Cerrar Sesión</a>
        </div>
      </header>

        <main>
            <!-- Formulario de Búsqueda y Creación -->
            <div id="form-section" class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Añadir o Buscar Libro</h2>
                <form id="book-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <input type="hidden" id="book-id">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-600 mb-1">Título<span class="text-red-500">*</span></label>
                        <input type="text" id="titulo" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label for="autor" class="block text-sm font-medium text-gray-600 mb-1">Autor(es) <span class="text-xs text-gray-400">(separados por coma)</span><span class="text-red-500">*</span></label>
                        <input type="text" id="autor" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label for="editorial" class="block text-sm font-medium text-gray-600 mb-1">Editorial</label>
                        <input type="text" id="editorial" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                     <div>
                        <label for="isbn" class="block text-sm font-medium text-gray-600 mb-1">ISBN <span class="text-xs text-gray-400">(si el libro existe se autocompletará automáticamente)</span></label>
                        <div class="relative">
                           <input type="text" id="isbn" class="w-full p-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                           <button type="button" id="isbn-search-btn" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                               <svg id="isbn-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="h-5 w-5 text-gray-400" viewBox="0 0 16 16">
                                  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                               </svg>
                               <div id="isbn-spinner" class="spinner hidden"></div>
                           </button>
                        </div>
                    </div>
                    <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-600 mb-1">Categoría<span class="text-red-500">*</span></label>
                    <select id="categoria" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></select>
                </div>
                <div>
                    <label for="genero" class="block text-sm font-medium text-gray-600 mb-1">Género<span class="text-red-500">*</span></label>
                    <select id="genero" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></select>
                </div>
                    <div>
                    <label for="edicion" class="block text-sm font-medium text-gray-600 mb-1">Edición</label>
                    <input type="text" id="edicion" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                     <div class="md:col-span-2">
                        <label for="comentario" class="block text-sm font-medium text-gray-600 mb-1">Comentario</label>
                        <textarea id="comentario" rows="3" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                    </div>
                     <div class="md:col-span-3">
                        <label for="portada" class="block text-sm font-medium text-gray-600 mb-1">Subir portada</label>
                        <div class="flex items-center space-x-4">
                            <input type="file" id="portada" name="portada" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <div id="portada-preview-container" class="hidden">
                                <img id="portada-preview-img" src="" alt="Vista previa de portada" class="h-16 w-auto rounded border">
                                <p class="text-xs text-gray-500 mt-1">Vista previa</p>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mt-6 flex flex-wrap gap-4 justify-center">
                    <button id="save-btn" class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">Guardar Nuevo Libro</button>
                    <button id="search-btn" class="bg-green-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-green-700 transition shadow-md">Buscar</button>
                    <button id="show-all-btn" class="bg-gray-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-gray-700 transition shadow-md">Mostrar Todo</button>
                    <button id="clear-btn" class="bg-yellow-500 text-white py-2 px-6 rounded-lg font-semibold hover:bg-yellow-600 transition shadow-md">Limpiar Formulario</button>
                </div>
            </div>

            <!-- Listado de Resultados -->
            <div id="results-section">
                 <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                    <h2 class="text-2xl font-semibold text-gray-700">Catálogo</h2>
                    <div class="flex items-center space-x-4">
                         <span id="record-count" class="text-gray-500 font-medium"></span>
                         <div class="flex items-center space-x-2">
                            <label for="per-page-select" class="text-sm font-medium text-gray-600">Registros por página:</label>
                            <select id="per-page-select" class="p-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 table-sortable">
                        <thead class="bg-gray-100">
                            <tr>
                                <th data-sort="id" class="p-3 text-sm font-semibold tracking-wide text-left">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Portada</th>
                                <th data-sort="titulo" class="p-3 text-sm font-semibold tracking-wide text-left">Título</th>
                                <th data-sort="autor1" class="p-3 text-sm font-semibold tracking-wide text-left">Autor</th>
                                <th data-sort="categoria_nombre" class="p-3 text-sm font-semibold tracking-wide text-left">Categoría</th>
                                <th data-sort="genero_nombre" class="p-3 text-sm font-semibold tracking-wide text-left">Género</th>
                                <th data-sort="isbn" class="p-3 text-sm font-semibold tracking-wide text-left">ISBN</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="results-body" class="divide-y divide-gray-200">
                             <tr><td colspan="8" class="text-center p-8"><div class="loader mx-auto"></div><p class="mt-2 text-gray-500">Cargando datos de la base de datos...</p></td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination-controls" class="mt-8 flex justify-center items-center space-x-1 md:space-x-2"></div>
            </div>
        </main>
    </div>

    <!-- Modal para Detalles y Edición -->
    <div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center p-4 z-50 hidden">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-11/12 md:w-2/3 lg:w-1/2 p-8 transform transition-all max-h-[90vh] overflow-y-auto">
             <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Detalle del Libro</h2>
                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
            </div>
            <form id="detail-form" class="space-y-4">
                 <input type="hidden" id="detail-book-id">
                 <div class="text-center mb-4">
                    <img id="detail-portada-img" src="https://placehold.co/400x600/EFEFEF/AAAAAA?text=Portada" alt="Portada" class="max-h-64 mx-auto rounded-lg shadow-md" onerror="this.onerror=null;this.src='https://placehold.co/400x600/EFEFEF/AAAAAA?text=Sin+Imagen';">
                </div>
                <div>
                    <label for="detail-titulo" class="block text-sm font-medium text-gray-600">Título<span class="text-red-500">*</span></label>
                    <input type="text" id="detail-titulo" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="detail-autor" class="block text-sm font-medium text-gray-600">Autor(es)<span class="text-red-500">*</span></label>
                    <input type="text" id="detail-autor" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="detail-categoria" class="block text-sm font-medium text-gray-600">Categoría<span class="text-red-500">*</span></label>
                        <select id="detail-categoria" class="mt-1 w-full p-2 border border-gray-300 rounded-md"></select>
                    </div>
                    <div>
                        <label for="detail-genero" class="block text-sm font-medium text-gray-600">Género<span class="text-red-500">*</span></label>
                        <select id="detail-genero" class="mt-1 w-full p-2 border border-gray-300 rounded-md"></select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="detail-editorial" class="block text-sm font-medium text-gray-600">Editorial</label>
                        <input type="text" id="detail-editorial" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="detail-edicion" class="block text-sm font-medium text-gray-600">Edición</label>
                        <input type="text" id="detail-edicion" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label for="detail-isbn" class="block text-sm font-medium text-gray-600">ISBN</label>
                    <input type="text" id="detail-isbn" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                </div>
                 <div>
                    <label for="detail-comentario" class="block text-sm font-medium text-gray-600">Comentario</label>
                    <textarea id="detail-comentario" rows="4" class="mt-1 w-full p-2 border border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label for="detail-portada-file" class="block text-sm font-medium text-gray-600">Cambiar Portada</label>
                    <input type="file" id="detail-portada-file" name="portada" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </form>
            <div class="mt-8 flex flex-wrap gap-4 justify-end">
                <button id="update-btn" class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">Guardar Cambios</button>
                <button id="delete-btn" class="bg-red-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-red-700 transition shadow-md">Eliminar</button>
                <button id="cancel-btn" class="bg-gray-300 text-gray-800 py-2 px-6 rounded-lg font-semibold hover:bg-gray-400 transition">Cerrar</button>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-5 right-5 text-white py-2 px-4 rounded-lg shadow-lg opacity-0 transition-opacity duration-300">
        <p id="toast-message"></p>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

