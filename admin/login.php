<?php

//////////////////ESTA CARPETA ES PARA HACER UN FORMULARIO, PARA QUE LOS USUARIOS QUE YA ESTAN REGISTRADOS PUEDAN METERSE EN LA API SERIA COMO UN INICIAR SESION Y ENTONCES TAMBIEN TENEMOS QUE MIRAR QUE EL USUARIO ESTE EN LA BASE DE DATOS PARA SABER QUE ESTA LOGUEADOS

/**
 * Para guardar los datos de una sesion en php se utiliza la variable superglobal
 * $_SESION es un array asociativo
 * 
 * Para poder utilizar esta variable tenemos que iniciar sesion
 * session_start()
 */

if(session_status() == PHP_SESSION_NONE){   ///////////esto se pone para poder utilizar despues la variable superglobal de $_SESSION[''] que hemos creado en otra carpeta
    session_start();
}


 //ejemplo de esto:
// session_start();
// $_SESSION['username'] = "Manolico";
//  var_dump($_SESSION);



 //comprobar si el ususario ya esta logueado, si esta logueado, redirigir a index

if(isset($_SESSION['logueado']) && $_SESSION['logueado'] == true){
    //redirigir a index
    header("Location: index.php");
    exit();
}

//mostrar un formulario que pida correo y contraseña

//comprobar que los datos sean correctos

//si son correctos iniciar sesion y redirigir a index
//si son incorrectos mostrar un mensaje de error



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="cs/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="../controllers/usuarioController.php">
            <input type="email" name="email" require placeholder="Correo electrónico">
            <input type="password" name="password" required placeholder="contraseña">
            <input type="submit" name="login" value="Iniciar Sesión">
        </form>
        
        <div class="olvido-password">
            <a class="abrir-modal-recuperar">Recuperar contraseña</a>
        </div>
        <div class="crear-cuenta">
            <a class="abrir-modal-registro">Crear cuenta nueva</a>
        </div>
        
        
        <?php
        if(isset($_SESSION['mensaje'])){
            echo "<div class='error'>" . $_SESSION['mensaje'] . "</div>";
            unset($_SESSION['mensaje']);
        }
        ?>
        <div id="modalRecuperar" class="modal">
            <div class="modal-contenido">
                <span class="cerrarRecuperar">&times;</span>
                <h2>Recuperar contraseña</h2>
                <form method="POST" action="../controllers/usuarioController.php">
                    <input type="email" name="email" required placeholder="Correo electrónico">
                    <input type="submit" name="recuperar" value="Recuperar Contraseña">
                </form>
            </div>
        </div>

        <div id="modalRegistro" class="modal">
            <div class="modal-contenido">
                <span class="cerrarRegistro">&times;</span>
                <h2>Registro Cuenta nueva</h2>
                <form method="POST" action="../controllers/usuarioController.php">
                    <input type="email" name="email" required placeholder="correo electrónico">
                    <input type="password" name="password" required placeholder="Contraseña">
                    <input type="submit" name="registro" value="Registrarse">
                </form>
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
    
</body>
</html>