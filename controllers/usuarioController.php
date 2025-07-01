


<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//AQUI TENGO TODO COMPLETO PARA QUE SIRVA CUANDO NOS LOGUEAMOS LA PARTE QUE SE CREA LA NUEVA CONTRASEÑA Y TODO ESO////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////







if(session_status() == PHP_SESSION_NONE){
    session_start();
}//para comprobar si la sesion esta activa



require_once '../config/database.php';
require_once '../data/usuarioDB.php';
//crear instancia UsuarioDB
$database = new Database();
$usuariodb =  new UsuarioDB($database);


//comprobar si el usuario quiere un inicio de sesion
//comprobar que llegan los datos: email, password y login
//comprobar que el metodo es post



if(
  $_SERVER['REQUEST_METHOD'] == 'POST'
  && isset($_POST['login'])
  && isset($_POST['email'])
  && isset($_POST['password'])
){
  //El usuario quiere iniciar sesion
  //comprobar que el usuario existe y que el password es correcto

  $email = $_POST['email'];
  $password = $_POST['password'];
  $resultado = $usuariodb->verificarCredenciales($email, $password);
  //guardar la respuesta
  $_SESSION['logueado'] = $resultado['success'];
  if($resultado['success'] == true){
    $_SESSION['usuario'] = $resultado['usuario'];
    $ruta = '../admin/index.php';
  }
  redirigirConMensaje($ruta, $resultado['success'], $resultado['mensaje']);
}

//comprobar si el usuario quiere registrarse 
if(
  $_SERVER['REQUEST_METHOD'] == 'POST'
  && isset($_POST['registro'])
  && isset($_POST['email'])
  && isset($_POST['password'])
){
  //el usuario quiere crear una cuenta nueva
   redirigirConMensaje('../admin/login.php', true, "Quieres crear una cuenta nueva?");
}


//comprobar si el usuario quiere recuperar la contraseña

if(
  $_SERVER['REQUEST_METHOD'] == 'POST'
  && isset($_POST['recuperar'])
  && isset($_POST['email'])
  
){
  //el usuario quiere recuperar la contraseña
   redirigirConMensaje('../admin/login.php', true, "Has olvidado la contraseña?");
}

function redirigirConMensaje($url, $success, $mensaje){
  //almacena el resultado en la variable de sesion
  $_SESSION['success'] = $success;
  $_SESSION['mensaje'] = $mensaje;

  //realizar la redireccion
  header("Location: $url");
}