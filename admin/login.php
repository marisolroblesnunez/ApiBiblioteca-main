<?php


/**
 * Para guardar los datos de una sesion en php se utiliza la variable superglobal
 * $_SESION es un array asociativo
 * 
 * Para poder utilizar esta variable tenemos que iniciar sesion
 * session_start()
 */

if(session_status() == PHP_SESSION_NONE){
    session_start();
}


 //ejemplo de esto:
// session_start();
// $_SESSION['username'] = "Manolico";
//  var_dump($_SESSION);



 //comprobar si el ususario ya esta logueado

//si está logueado redirigir a index

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
        <?php
        if(isset($_SESSION['mensaje'])){
            echo "<div class='error'>" . $_SESSION['mensaje'] . "</div>";
            unset($_SESSION['mensaje']);
        }
        ?>
    </div>
    
</body>
</html>