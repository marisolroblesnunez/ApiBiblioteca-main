<?php

//si le llega el token, le muestra un formulario para que meta la contraseña un par de veces, nueva contraseña y restablecer contraseña

include_once '../data/usuarioDB.php';
require_once '../config/database.php';

$database = new Database();
$usuariobd = new UsuarioDB($database);

// verificar si se ha proporcionado un token
if(isset($_GET['token'])){
    $token = $_GET['token'];

    if($_SERVER['REQUEST_METHOD'] == "POST" 
    && isset($_POST['nueva_password'])
    && isset($_POST['confirmar_password'])){
        $resultado = $usuariobd->restablecerPassword($token, $_POST['nueva_password']);
        $mensaje = $resultado['mensaje'];
    }
}else{
    header("Location: login.php");
    exit();
}


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="cs/login.css">
</head>
<body>
    <div class="container">
    <h1>Restablecer Contraseña</h1>
    <?php
        if(!empty($mensaje)): ?>
        <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php if($resultado['success']): ?>
            <a href="login.php" class="volver">Ir a Iniciar Sesión</a>
        <?php endif; 
        else: 
        ?>
        <form method="POST" id="formRestablecer">
            <input type="password" name="nueva_password" id="nueva_password" required placeholder="Nueva Contraseña">
            <input type="password" name="confirmar_password" id="confirmar_password" required placeholder="Confirmar Nueva Contraseña">
            <input type="submit" value="Restablecer Contraseña">
            <p class="error" id="mensaje_cliente"></p>
        </form>
        <?php endif; ?>
  </div>
 <script src="js/restablecer.js"></script>
</body>
</html>
        <!-- 
        otra forma
        /**
        *si tengo un mensaje: Muestro el mensaje y un enlace a iniciar session
        *si no tengo mensaje: muestro un formulario para introducir la nueva contraseña
        -->
        <?php
       // if(!empty($mensaje)){
        //    echo '<p class=mensaje>'. $mensaje .'</p>;
        //    if($resultado['success']){
       //     echo '<a href="login.php" class="volver">Ir a iniciar Sesión</a>';
      //  }
     //   }else{ 
     //   echo '
    // <form method="POST">
    // <input type="password" name="nueva_password" required placeholder="Nueva Contraseña">
    // <input type= "password"name="confirmar_password" required placeholder="confirmar nueva contraseña">
    // <input type="submit"value="Restablecer contraseña">
    // </form><?
  