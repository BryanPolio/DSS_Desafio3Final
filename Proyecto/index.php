<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroVerduras - Acceso</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            padding: 20px;
        }
        h1 { color: var(--verde-oscuro); text-align: center; margin-bottom: 40px; font-size: 2.5em; }
        h2 { color: var(--cafe-tierra); margin-top: 0; border-bottom: 2px solid var(--verde-claro); padding-bottom: 10px; }
        
        .contenedor { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 40px; 
            justify-content: center; 
            align-items: flex-start;
        }
        .caja { 
            background: white; 
            border-top: 5px solid var(--verde-hoja); 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            width: 100%; 
            max-width: 350px; 
        }
        
        label { font-weight: bold; color: var(--verde-oscuro); font-size: 0.9em; }
        input[type="text"], input[type="password"], input[type="file"] { 
            width: 100%; 
            padding: 10px; 
            margin: 8px 0 20px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box; 
            transition: 0.3s;
        }
        input:focus { border-color: var(--verde-hoja); outline: none; box-shadow: 0 0 5px rgba(76, 175, 80, 0.3); }
        
        button { 
            width: 100%; 
            background-color: var(--naranja); 
            color: white; 
            padding: 12px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 16px; 
            transition: 0.3s;
        }
        button:hover { background-color: #e68a00; transform: translateY(-2px); }
        
        .mensaje { margin-top: 15px; font-weight: bold; text-align: center; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>🌱 AgroVerduras S.A.</h1>
    
    <div class="contenedor">
        <div class="caja">
            <h2>Iniciar Sesión</h2>
            <form id="loginForm">
                <label>Usuario:</label>
                <input type="text" name="usuario" required>
                
                <label>Contraseña:</label>
                <input type="password" name="password" required>
                
                <button type="submit">Ingresar</button>
            </form>
            <div id="msg_login" class="mensaje" style="color: #d32f2f;"></div>
        </div>

        <div class="caja">
            <h2>Crear Usuario</h2>
            <form id="registroForm" enctype="multipart/form-data">
                <label>Nombre Completo:</label>
                <input type="text" name="nombre" required>
                
                <label>Usuario:</label>
                <input type="text" name="usuario" required>
                
                <label>Contraseña:</label>
                <input type="password" name="password" required>

                <label>Foto de Perfil (JPG/PNG):</label>
                <input type="file" name="foto" accept="image/png, image/jpeg" required>
                
                <button type="submit" style="background-color: var(--verde-hoja);">Registrarme</button>
            </form>
            <div id="msg_registro" class="mensaje"></div>
        </div>
    </div>

    <script>
        // AJAX PARA EL LOGIN
        $('#loginForm').submit(function(e){
            e.preventDefault(); 
            $.ajax({
                type: 'POST',
                url: 'validar_login.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.exito) window.location.href = 'dashboard.php';
                    else $('#msg_login').text(res.mensaje);
                }
            });
        });

        // AJAX PARA EL REGISTRO
        $('#registroForm').submit(function(e){
            e.preventDefault();
            var formData = new FormData(this); 

            $.ajax({
                type: 'POST',
                url: 'procesar_registro.php',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    if(res.exito) {
                        $('#msg_registro').css('color', '#388E3C').text(res.mensaje);
                        $('#registroForm')[0].reset();
                    } else {
                        $('#msg_registro').css('color', '#d32f2f').text(res.mensaje);
                    }
                }
            });
        });
    </script>
</body>
</html>