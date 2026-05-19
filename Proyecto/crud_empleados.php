<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$conexion = new PDO("mysql:host=localhost;dbname=sistema_verduras", "root", "");

$mensaje = "";
$error = "";

// Eliminar empleado
if (isset($_GET['eliminar'])) {
    $stmt = $conexion->prepare("DELETE FROM empleados WHERE id = ?");
    $stmt->execute([$_GET['eliminar']]);
    $mensaje = "Registro eliminado correctamente.";
}

// Agregar o modificar empleado con validaciones estrictas y mensajes detallados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $codigo = trim($_POST['codigo_empleado']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $fecha = $_POST['fecha_contratacion'];
    $salario = $_POST['salario_diario'];
    $area = $_POST['area_trabajo'];
    $cultivo = trim($_POST['cultivo_asignado']);
    $telefono = trim($_POST['telefono']);
    $edad = (int)$_POST['edad']; 
    $turno = $_POST['turno']; 

    // Validaciones estrictas con mensajes detallados php
    $errores_array = [];

    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombres)) $errores_array[] = "- Los nombres solo deben contener letras.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellidos)) $errores_array[] = "- Los apellidos solo deben contener letras.";

    // Que el código de empleado sea: 4-6 caracteres, letras o números, sin espacios ni símbolos
    if (!preg_match("/^[a-zA-Z0-9]{4,6}$/", $codigo)) {
        $errores_array[] = "- El código debe tener entre 4 y 6 letras o números, sin espacios ni símbolos.";
    }

    if (!preg_match("/^[267][0-9]{7}$/", $telefono)) $errores_array[] = "- El teléfono debe tener 8 dígitos (empezando con 2, 6 o 7).";
    if ($salario < 100 || $salario > 800) $errores_array[] = "- El salario diario debe estar entre $100 y $800.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $cultivo)) $errores_array[] = "- El cultivo asignado solo debe contener letras.";
    
    if (!in_array($area, ['Cosecha', 'Empaque', 'Bodega'])) $errores_array[] = "- El área seleccionada no es válida.";
    
    
    if (!in_array($turno, ['Diurno', 'Nocturno'])) $errores_array[] = "- El turno seleccionado no es válido.";

    $fecha_actual = date("Y-m-d");
    if($fecha < "2000-01-01" || $fecha > $fecha_actual) $errores_array[] = "- La fecha de contratación es inválida.";
    if ($edad < 18 || $edad > 75) $errores_array[] = "- La edad debe estar entre 18 y 75 años.";

   if (count($errores_array) > 0) {
        $error = "<b>Se encontraron los siguientes errores:</b><br>" . implode("<br>", $errores_array);
    } else {
        
        try {
            if (empty($id)) {
                $stmt = $conexion->prepare("INSERT INTO empleados (codigo_empleado, nombres, apellidos, fecha_contratacion, salario_diario, area_trabajo, cultivo_asignado, telefono, edad, turno) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codigo, $nombres, $apellidos, $fecha, $salario, $area, $cultivo, $telefono, $edad, $turno]);
                $mensaje = "Empleado registrado exitosamente.";
            } else {
                $stmt = $conexion->prepare("UPDATE empleados SET codigo_empleado=?, nombres=?, apellidos=?, fecha_contratacion=?, salario_diario=?, area_trabajo=?, cultivo_asignado=?, telefono=?, edad=?, turno=? WHERE id=?");
                $stmt->execute([$codigo, $nombres, $apellidos, $fecha, $salario, $area, $cultivo, $telefono, $edad, $turno, $id]);
                $mensaje = "Datos del empleado actualizados.";
            }
        } catch(PDOException $e) {
            // El código 1062 es el error de MySQL para "Duplicate entry" (Entrada duplicada)
            if ($e->errorInfo[1] == 1062) {
                $error = "<b>Error en el código del Trabajador:</b> El Código de Empleado '{$codigo}' ya existe en el sistema. Y este debe ser único para cada trabajador.";
            } else {
                $error = "Error inesperado de base de datos: " . $e->getMessage();
            }
        }
    }
}

$empleado_editar = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $empleado_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personal - AgroVerduras</title>
    <style>
        :root { --verde-claro: #f1f8e9; --verde-hoja: #4CAF50; --verde-oscuro: #2E7D32; --cafe-tierra: #795548; --naranja: #FF9800; --fondo: #fafafa; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--fondo); color: #333; padding: 20px; }
        h2, h3 { color: var(--cafe-tierra); }
        .btn { background-color: var(--verde-hoja); color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn:hover { background-color: var(--verde-oscuro); }
        .btn-cancel { background-color: var(--naranja); text-decoration: none; padding: 10px 15px; border-radius: 5px; color: white; font-weight: bold;}
        .contenedor-formulario { background: white; border-top: 4px solid var(--verde-hoja); padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: var(--cafe-tierra);}
        .form-group input, .form-group select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9em; }
        th { background-color: var(--verde-claro); color: var(--verde-oscuro); font-weight: bold; }
        .alerta-ok { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 5px solid #28a745; }
        .alerta-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 5px solid #dc3545; }
    </style>
</head>
<body>
    <a href="dashboard.php" style="color: var(--verde-oscuro); text-decoration: none;"><b>← Volver al Panel</b></a>
    <h2>Gestión de Personal Agrícola</h2>
    
    <?php if($mensaje) echo "<div class='alerta-ok'>$mensaje</div>"; ?>
    <?php if($error) echo "<div class='alerta-error'>$error</div>"; ?>

    <div class="contenedor-formulario">
        <h3><?php echo $empleado_editar ? 'Modificar Empleado' : '🌱 Registrar Nuevo Empleado'; ?></h3>
        
        <form method="POST" action="crud_empleados.php">
            <input type="hidden" name="id" value="<?php echo $empleado_editar['id'] ?? ''; ?>">
            
            <div class="grid-form">
              <div class="form-group">
                    <label>Código de Empleado (4 a 6 caracteres):</label>
                    <input type="text" name="codigo_empleado" pattern="^[a-zA-Z0-9]{4,6}$" maxlength="6" title="Debe tener entre 4 y 6 letras o números, sin espacios." value="<?php echo $empleado_editar['codigo_empleado'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Nombres:</label>
                    <input type="text" name="nombres" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$" value="<?php echo $empleado_editar['nombres'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Apellidos:</label>
                    <input type="text" name="apellidos" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$" value="<?php echo $empleado_editar['apellidos'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Fecha Contratación:</label>
                    <input type="date" name="fecha_contratacion" min="2000-01-01" max="<?php echo date('Y-m-d'); ?>" value="<?php echo $empleado_editar['fecha_contratacion'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Salario Diario ($):</label>
                    <input type="number" step="0.01" min="100" max="800" name="salario_diario" value="<?php echo $empleado_editar['salario_diario'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Área de Trabajo:</label>
                    <select name="area_trabajo" required>
                        <option value="Cosecha" <?php echo (isset($empleado_editar) && $empleado_editar['area_trabajo'] == 'Cosecha') ? 'selected' : ''; ?>>Cosecha</option>
                        <option value="Empaque" <?php echo (isset($empleado_editar) && $empleado_editar['area_trabajo'] == 'Empaque') ? 'selected' : ''; ?>>Empaque</option>
                        <option value="Bodega" <?php echo (isset($empleado_editar) && $empleado_editar['area_trabajo'] == 'Bodega') ? 'selected' : ''; ?>>Bodega</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cultivo Asignado:</label>
                    <input type="text" name="cultivo_asignado" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$" value="<?php echo $empleado_editar['cultivo_asignado'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Teléfono (8 dígitos):</label>
                    <input type="text" name="telefono" pattern="^[267][0-9]{7}$" value="<?php echo $empleado_editar['telefono'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Edad (Años):</label>
                    <input type="number" min="18" max="75" name="edad" value="<?php echo $empleado_editar['edad'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Turno de Trabajo:</label>
                    <select name="turno" required>
                        <option value="Diurno" <?php echo (isset($empleado_editar) && $empleado_editar['turno'] == 'Diurno') ? 'selected' : ''; ?>>☀️ Diurno</option>
                        <option value="Nocturno" <?php echo (isset($empleado_editar) && $empleado_editar['turno'] == 'Nocturno') ? 'selected' : ''; ?>>🌙 Nocturno</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn"><?php echo $empleado_editar ? 'Actualizar Datos' : 'Guardar Empleado'; ?></button>
                <?php if($empleado_editar): ?>
                    <a href="crud_empleados.php" class="btn-cancel">Cancelar edición</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <h3>Directorio de Empleados</h3>
    <table>
        <tr>
            <th>Código</th><th>Nombres</th><th>Apellidos</th><th>Fecha</th><th>Salario</th><th>Área</th><th>Cultivo</th><th>Teléfono</th><th>Edad</th><th>Turno</th><th>Acciones</th>
        </tr>
        <?php
        $stmt = $conexion->query("SELECT * FROM empleados ORDER BY id DESC");
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><b>{$fila['codigo_empleado']}</b></td>";
            echo "<td>{$fila['nombres']}</td>";
            echo "<td>{$fila['apellidos']}</td>";
            echo "<td>{$fila['fecha_contratacion']}</td>";
            echo "<td>$" . number_format($fila['salario_diario'], 2) . "</td>";
            echo "<td>{$fila['area_trabajo']}</td>";
            echo "<td>{$fila['cultivo_asignado']}</td>";
            echo "<td>{$fila['telefono']}</td>";
            echo "<td>{$fila['edad']}</td>";
            echo "<td>{$fila['turno']}</td>"; 
            echo "<td>
                    <a href='crud_empleados.php?editar={$fila['id']}' style='color: var(--naranja); text-decoration: none; font-weight: bold;'>Editar</a> | 
                    <a href='crud_empleados.php?eliminar={$fila['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar?\")' style='color: red; text-decoration: none; font-weight: bold;'>Eliminar</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>