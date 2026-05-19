<?php
$conexion = new PDO("mysql:host=localhost;dbname=sistema_verduras", "root", "");

$nombre = $_POST['nombre'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// Validar campos de texto
if(empty($nombre) || empty($usuario) || empty($password)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Campos de texto incompletos.']);
    exit;
}

// Validar que se haya subido la imagen sin errores
if(!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al subir la fotografía.']);
    exit;
}

// Lógica de la imagen
$foto = $_FILES['foto'];
$ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
$extensiones_permitidas = ['jpg', 'jpeg', 'png'];

if(!in_array($ext, $extensiones_permitidas)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Solo se permiten imágenes JPG o PNG.']);
    exit;
}

// Generar un nombre único para la foto para que no se sobreescriban
$nombre_foto_final = uniqid() . "." . $ext;
$ruta_destino = "uploads/" . $nombre_foto_final;

// Muevo la imagen de la memoria temporal a nuestra carpeta
if(move_uploaded_file($foto['tmp_name'], $ruta_destino)) {
    
    // Encriptar contraseña y guardar en base de datos
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password, nombre_completo, foto) VALUES (:user, :pass, :nom, :foto)");
        $stmt->bindParam(':user', $usuario);
        $stmt->bindParam(':pass', $password_encriptada);
        $stmt->bindParam(':nom', $nombre);
        $stmt->bindParam(':foto', $nombre_foto_final);
        $stmt->execute();
        
        echo json_encode(['exito' => true, 'mensaje' => 'Usuario y foto guardados con éxito.']);
    } catch(PDOException $e) {
        // Si el usuario ya existe, PDO lanza error porque le pusimos UNIQUE en SQL
        echo json_encode(['exito' => false, 'mensaje' => 'El nombre de usuario ya existe.']);
    }

} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar la imagen en el servidor.']);
}
?>