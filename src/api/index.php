<?php
session_start();

// --- GESTIÓN DE ERRORES ROBUSTA ---
ini_set('display_errors', 0);
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// --- ENRUTADOR DE ACCIONES ---
$action = $_POST['action'] ?? ($_GET['action'] ?? '');

try {
    header('Content-Type: application/json');

    // --- CONEXIÓN A BBDD ---
    require_once '../db/database.php'; // Incluir la conexión desde la nueva ubicación

    // La acción de login es la única permitida sin una sesión activa
    if ($action !== 'login' && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)) {
        http_response_code(401); // No autorizado
        echo json_encode(['success' => false, 'error' => 'Acceso no autorizado.']);
        $conn->close();
        exit;
    }

    // --- CONFIGURACIÓN DE RUTAS ---
    $baseDir = '../../public/images'; // Ruta relativa desde api/index.php
    $portadasDir = $baseDir . '/portadas';
    $miniaturasDir = $baseDir . '/miniaturas';

    // Crear directorios si no existen
    if (!is_dir($portadasDir)) mkdir($portadasDir, 0777, true);
    if (!is_dir($miniaturasDir)) mkdir($miniaturasDir, 0777, true);
    
    // -- Verificación y ajuste de la BBDD (se ejecuta una vez) --
    if ($action !== 'login') {
         checkAndUpdateDatabaseSchema($conn);
    }
    

    // --- GESTIÓN DE PETICIONES ---
    switch ($action) {
        case 'login':
            handleLogin($conn);
            break;
        case 'read':
            handleRead($conn);
            break;
        case 'create':
            handleCreate($conn, $portadasDir, $miniaturasDir);
            break;
        case 'update':
            handleUpdate($conn, $portadasDir, $miniaturasDir);
            break;
        case 'delete':
            handleDelete($conn, $portadasDir, $miniaturasDir);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }

    $conn->close();

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Ha ocurrido un error en el servidor.',
        'details' => $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine()
    ]);
}

// --- FUNCIONES LÓGICAS ---

function handleLogin($conn) {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($usuario) || empty($contrasena)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Usuario y contraseña son requeridos.']);
        return;
    }

    $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE usuario = ?");
    if ($stmt === false) throw new Exception("Error al preparar la consulta de login: " . $conn->error);
    
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Comparación directa de contraseñas (NO RECOMENDADO PARA PRODUCCIÓN)
        if ($contrasena === $user['contrasena']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $usuario;
            echo json_encode(['success' => true]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta.']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
    }
    $stmt->close();
}


function handleRead($conn) {
    $limit_whitelist = [10, 25, 50, 100];
    $limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_whitelist) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    
    $sort_by_whitelist = ['id', 'titulo', 'autor1', 'categoria_nombre', 'genero_nombre', 'isbn'];
    $sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $sort_by_whitelist) ? $_GET['sort_by'] : 'id';
    $sort_order = isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc' ? 'ASC' : 'DESC';

    $baseQuery = "FROM libro l LEFT JOIN categoria c ON l.categoria = c.id LEFT JOIN genero g ON l.genero = g.id";
    
    $whereClauses = [];
    $params = [];
    $types = '';

    if (!empty($_GET['titulo'])) { $whereClauses[] = "l.titulo LIKE ?"; $params[] = '%' . $_GET['titulo'] . '%'; $types .= 's'; }
    if (!empty($_GET['autor'])) { $whereClauses[] = "(l.autor1 LIKE ? OR l.autor2 LIKE ? OR l.autor3 LIKE ?)"; $param = '%' . $_GET['autor'] . '%'; $params = array_merge($params, [$param, $param, $param]); $types .= 'sss'; }
    if (!empty($_GET['categoria'])) { $whereClauses[] = "l.categoria = ?"; $params[] = $_GET['categoria']; $types .= 'i'; }
    if (!empty($_GET['genero'])) { $whereClauses[] = "l.genero = ?"; $params[] = $_GET['genero']; $types .= 'i'; }
    if (!empty($_GET['editorial'])) { $whereClauses[] = "l.editorial LIKE ?"; $params[] = '%' . $_GET['editorial'] . '%'; $types .= 's'; }
    if (!empty($_GET['isbn'])) { $whereClauses[] = "l.isbn LIKE ?"; $params[] = '%' . $_GET['isbn'] . '%'; $types .= 's'; }
    
    $whereSql = !empty($whereClauses) ? " WHERE " . implode(' AND ', $whereClauses) : "";
    
    $countSql = "SELECT COUNT(*) as total " . $baseQuery . $whereSql;
    $countStmt = $conn->prepare($countSql);
    if ($countStmt === false) throw new Exception("Error al preparar la consulta de conteo: " . $conn->error);
    if (!empty($params)) $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalRecords = (int)$countStmt->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $limit);
    $countStmt->close();

    $dataSql = "SELECT l.*, c.nombreCategoria as categoria_nombre, g.nombreGenero as genero_nombre " . $baseQuery . $whereSql . " ORDER BY {$sort_by} {$sort_order}, l.id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($dataSql);
    if ($stmt === false) throw new Exception("Error al preparar la consulta de datos: " . $conn->error);
    $stmt->bind_param($types . 'ii', ...array_merge($params, [$limit, $offset]));
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['data' => $books, 'total_pages' => $totalPages, 'total_records' => $totalRecords, 'current_page' => $page]);
    $stmt->close();
}

function handleCreate($conn, $portadasDir, $miniaturasDir) {
    $portadaName = handleImageProcessing($_FILES['portada'] ?? null, $_POST['cover_url'] ?? null, $portadasDir, $miniaturasDir);

    $stmt = $conn->prepare("INSERT INTO libro (titulo, autor1, autor2, autor3, editorial, edicion, genero, categoria, isbn, comentario, portada) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) throw new Exception("Error al preparar la consulta INSERT: " . $conn->error);
    
    $genero = !empty($_POST['genero']) && (int)$_POST['genero'] > 1 ? (int)$_POST['genero'] : 1;
    $categoria = !empty($_POST['categoria']) && (int)$_POST['categoria'] > 1 ? (int)$_POST['categoria'] : 1;
    
    $stmt->bind_param("ssssssiisss", $_POST['titulo'], $_POST['autor1'], $_POST['autor2'], $_POST['autor3'], $_POST['editorial'], $_POST['edicion'], $genero, $categoria, $_POST['isbn'], $_POST['comentario'], $portadaName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        throw new Exception("Error en la ejecución de la BBDD (Create): " . $stmt->error);
    }
    $stmt->close();
}

function handleUpdate($conn, $portadasDir, $miniaturasDir) {
    $id = $_POST['id'] ?? 0;
    if (!$id) throw new Exception("ID de libro no proporcionado para actualizar.");

    $portadaName = handleImageProcessing($_FILES['portada'] ?? null, null, $portadasDir, $miniaturasDir, $id, $conn);
    
    $sql = "UPDATE libro SET titulo=?, autor1=?, autor2=?, autor3=?, editorial=?, edicion=?, genero=?, categoria=?, isbn=?, comentario=?";
    $types = "ssssssiiss";
    $params = [$_POST['titulo'], $_POST['autor1'], $_POST['autor2'], $_POST['autor3'], $_POST['editorial'], $_POST['edicion'], (!empty($_POST['genero']) ? (int)$_POST['genero'] : 1), (!empty($_POST['categoria']) ? (int)$_POST['categoria'] : 1), $_POST['isbn'], $_POST['comentario']];

    if ($portadaName !== null) {
        $sql .= ", portada=?";
        $types .= "s";
        $params[] = $portadaName;
    }
    
    $sql .= " WHERE id=?";
    $types .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    if ($stmt === false) throw new Exception("Error al preparar la consulta UPDATE: " . $conn->error);
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error en la ejecución de la BBDD (Update): " . $stmt->error);
    }
    $stmt->close();
}

function handleDelete($conn, $portadasDir, $miniaturasDir) {
    $id = $_POST['id'] ?? 0;
    if (!$id) throw new Exception("ID de libro no proporcionado para eliminar.");

    $stmt = $conn->prepare("SELECT portada FROM libro WHERE id = ?");
    if ($stmt === false) throw new Exception("Error al preparar la consulta SELECT para eliminar: " . $conn->error);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $deleteStmt = $conn->prepare("DELETE FROM libro WHERE id = ?");
    if ($deleteStmt === false) throw new Exception("Error al preparar la consulta DELETE: " . $conn->error);
    $deleteStmt->bind_param("i", $id);
    
    if ($deleteStmt->execute()) {
        if ($result && !empty($result['portada'])) {
            $portadaPath = $portadasDir . '/' . $result['portada'];
            $miniaturaPath = $miniaturasDir . '/' . $result['portada'];
            if (file_exists($portadaPath)) unlink($portadaPath);
            if (file_exists($miniaturaPath)) unlink($miniaturaPath);
        }
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error en la ejecución de la BBDD (Delete): " . $deleteStmt->error);
    }
    $deleteStmt->close();
}

function downloadImageToTemp($url) {
    $imageData = @file_get_contents($url);
    if ($imageData === false) {
        return null;
    }
    $tempPath = tempnam(sys_get_temp_dir(), 'cover');
    file_put_contents($tempPath, $imageData);
    return $tempPath;
}

function handleImageProcessing($file, $url, $portadasDir, $miniaturasDir, $bookId = null, $conn = null) {
    $sourcePath = null;
    $tempFileToDelete = null;

    if ($file !== null && $file['error'] === UPLOAD_ERR_OK) {
        $sourcePath = $file['tmp_name'];
    } elseif ($url !== null && filter_var($url, FILTER_VALIDATE_URL)) {
        $sourcePath = downloadImageToTemp($url);
        $tempFileToDelete = $sourcePath;
    } else {
        return null; // No hay imagen para procesar
    }

    if ($sourcePath === null) return null;

    if ($bookId && $conn) {
        $stmt = $conn->prepare("SELECT portada FROM libro WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $oldImages = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($oldImages && !empty($oldImages['portada'])) {
             if (file_exists($portadasDir . '/' . $oldImages['portada'])) unlink($portadasDir . '/' . $oldImages['portada']);
             if (file_exists($miniaturasDir . '/' . $oldImages['portada'])) unlink($miniaturasDir . '/' . $oldImages['portada']);
        }
    }
    
    if ($file) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    } else {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        $ext = $pathInfo['extension'] ?? 'jpg';
    }
    
    $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
    if (empty($ext)) $ext = 'jpg';

    $fileName = 'portada_' . uniqid() . '.' . $ext;

    $portadaPath = $portadasDir . '/' . $fileName;
    $miniaturaPath = $miniaturasDir . '/' . $fileName;

    if (!resizeAndSaveImage($sourcePath, $portadaPath, 1280)) {
         if ($tempFileToDelete) unlink($tempFileToDelete);
         throw new Exception("No se pudo guardar la imagen de portada.");
    }
    if (!resizeAndSaveImage($sourcePath, $miniaturaPath, 80)) {
         if ($tempFileToDelete) unlink($tempFileToDelete);
         throw new Exception("No se pudo guardar la miniatura.");
    }
    
    if ($tempFileToDelete) {
        unlink($tempFileToDelete);
    }
    return $fileName;
}

function resizeAndSaveImage($sourcePath, $destinationPath, $maxSize, $quality = 85) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    list($width, $height, $type) = $imageInfo;
    $sourceImage = null;
    switch ($type) {
        case IMAGETYPE_JPEG: $sourceImage = imagecreatefromjpeg($sourcePath); break;
        case IMAGETYPE_PNG: $sourceImage = imagecreatefrompng($sourcePath); break;
        case IMAGETYPE_GIF: $sourceImage = imagecreatefromgif($sourcePath); break;
        default: return false;
    }
     if(!$sourceImage) return false;
    if ($width > $height) {
        if($width > $maxSize){
            $newWidth = $maxSize;
            $newHeight = floor($height * ($maxSize / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
    } else {
        if($height > $maxSize){
            $newHeight = $maxSize;
            $newWidth = floor($width * ($maxSize / $height));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
    }
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    if($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF){
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
    }
    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    $success = false;
    $destExt = strtolower(pathinfo($destinationPath, PATHINFO_EXTENSION));
    if ($destExt === 'gif' && $type === IMAGETYPE_GIF) {
        $success = imagegif($thumb, $destinationPath);
    } else if ($destExt === 'png' && $type === IMAGETYPE_PNG) {
         $success = imagepng($thumb, $destinationPath, 9); 
    }
    else {
         $success = imagejpeg($thumb, $destinationPath, $quality);
    }
    imagedestroy($sourceImage);
    imagedestroy($thumb);
    return $success;
}

function checkAndUpdateDatabaseSchema($conn) {
    $result = $conn->query("SHOW COLUMNS FROM `libro` LIKE 'miniatura'");
    if ($result && $result->num_rows > 0) {
        $conn->query("ALTER TABLE `libro` DROP COLUMN `miniatura`");
    }

    $result = $conn->query("SHOW COLUMNS FROM `libro` WHERE Field = 'portada'");
    if ($result) {
        $column = $result->fetch_assoc();
        if (strtoupper($column['Type']) !== 'VARCHAR(255)') { // Using VARCHAR is better than MEDIUMTEXT for file names
            $conn->query("ALTER TABLE `libro` MODIFY `portada` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        }
    }
}
?>

