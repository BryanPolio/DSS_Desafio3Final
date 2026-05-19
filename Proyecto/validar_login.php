<?php
session_start();
$conexion = new PDO("mysql:host=localhost;dbname=sistema_verduras", "root", "");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = :user");
$stmt->bindParam(':user', $usuario);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if($user_data && password_verify($password, $user_data['password'])) {
    $_SESSION['usuario_id'] = $user_data['id'];
    $_SESSION['nombre_completo'] = $user_data['nombre_completo'];
    $_SESSION['foto'] = $user_data['foto']; // Guardamos el nombre de la foto en sesión
    
    echo json_encode(['exito' => true]);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Credenciales incorrectas.']);
}
?>