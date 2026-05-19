<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Registro de Nuevo Usuario</h2>
    <form id="registroForm">
        <label>Nombre Completo:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label>Nuevo Usuario:</label>
        <input type="text" id="usuario" name="usuario" required><br><br>
        
        <label>Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Registrarme</button>
    </form>
    <div id="mensaje" style="margin-top: 10px; font-weight: bold;"></div>
    
    <br>
    <a href="index.php">Volver al Login</a>

    <script>
        $('#registroForm').submit(function(e){
            e.preventDefault(); 
            
            $.ajax({
                type: 'POST',
                url: 'procesar_registro.php',
                data: $(this).serialize(), // Empaqueta los datos del formulario y se envian con post
                dataType: 'json',


                success: function(respuesta) {
                    if(respuesta.exito) {
                        $('#mensaje').css('color', 'green').text(respuesta.mensaje);
                        $('#registroForm')[0].reset(); // Limpia el formulario
                    } else {
                        $('#mensaje').css('color', 'red').text(respuesta.mensaje);
                    }
                }
            });
        });
    </script>
</body>
</html>