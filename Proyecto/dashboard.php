<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - AgroVerduras</title>
    <style>
        :root {
            --verde-claro: #f1f8e9;
            --verde-hoja: #4CAF50;
            --verde-oscuro: #2E7D32;
            --cafe-tierra: #795548;
            --naranja: #FF9800;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--verde-claro); 
            color: #333; 
            margin: 0; 
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .contenedor {
            width: 100%;
            max-width: 800px;
        }
        
        .tarjeta-perfil { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            display: flex; 
            align-items: center; 
            gap: 25px; 
            border-left: 6px solid var(--naranja); 
            margin-bottom: 40px; 
        }
        .foto-perfil {
            width: 120px; 
            height: 120px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 4px solid var(--verde-hoja);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info-perfil h2 { color: var(--verde-oscuro); margin: 0 0 5px 0; font-size: 2em;}
        .info-perfil p { color: #666; margin: 0; font-size: 1.1em; }
        
      
        h3 { color: var(--cafe-tierra); text-align: center; margin-bottom: 20px; font-size: 1.5em; }
        .grid-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .btn-menu {
            background-color: var(--verde-hoja);
            color: white;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1em;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .btn-menu:hover { background-color: var(--verde-oscuro); transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        
        .btn-logout { background-color: #d32f2f; }
        .btn-logout:hover { background-color: #b71c1c; }
        .btn-pdf { background-color: var(--cafe-tierra); }
        .btn-pdf:hover { background-color: #5d4037; }
    </style>
</head>
<body>

    <div class="contenedor">
        <div class="tarjeta-perfil">
            <img src="uploads/<?php echo htmlspecialchars($_SESSION['foto']); ?>" alt="Foto de Perfil" class="foto-perfil">
            <div class="info-perfil">
                <h2>Hola, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h2>
                <p>👨‍🌾 Departamento de Recursos Humanos</p>
                <p>AgroVerduras S.A.</p>
            </div>
        </div>
        
        <h3>¿Qué deseas hacer hoy?</h3>
        
        <div class="grid-menu">
            <a href="crud_empleados.php" class="btn-menu">🌿 Administrar Empleados</a>
            <a href="reporte_pdf.php" class="btn-menu btn-pdf" target="_blank">📄 Generar Reporte PDF</a>
            <a href="logout.php" class="btn-menu btn-logout">🚪 Cerrar Sesión</a>
        </div>
    </div>

</body>
</html>