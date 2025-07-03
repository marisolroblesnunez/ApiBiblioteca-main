


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
$usuariobd =  new UsuarioDB($database);


//comprobar si el usuario quiere un inicio de sesion
//comprobar que llegan los datos: email, password y login
//comprobar que el metodo es post


//inicio de sesion
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
  $resultado = $usuariobd->verificarCredenciales($email, $password);
  //guardar la respuesta
  $_SESSION['logueado'] = $resultado['success'];
  if($resultado['success'] == true){
    $_SESSION['usuario'] = $resultado['usuario'];
    $ruta = '../admin/index.php';
  }else{
    $ruta = '../admin/login.php';
  }
  redirigirConMensaje($ruta, $resultado['success'], $resultado['mensaje']);
}

//comprobar si el usuario quiere registrarse registro de nuevo usuario
if(
  $_SERVER['REQUEST_METHOD'] == 'POST'
  && isset($_POST['registro'])
  && isset($_POST['email'])
  && isset($_POST['password'])
){

  $email = $_POST['email'];
  $password = $_POST['password'];

  $resultado = $usuariobd->registrarUsuario($email,$password);
  //el usuario quiere crear una cuenta nueva
   redirigirConMensaje('../admin/login.php', $resultado['success'], $resultado['mensaje']);
}


//comprobar si el usuario quiere recuperar la contraseña

if(
  $_SERVER['REQUEST_METHOD'] == 'POST'
  && isset($_POST['recuperar']) 
  && isset($_POST['email'])
  ){
    $email = $_POST['email'];
    
    $resultado = $usuariobd->recuperarPassword($email);
    redirigirConMensaje('../admin/login.php', $resultado['success'], $resultado['mensaje']);
  }
 
function redirigirConMensaje($url, $success, $mensaje){
  //almacena el resultado en la variable de sesion
  $_SESSION['success'] = $success;
  $_SESSION['mensaje'] = $mensaje;

  //realizar la redireccion
  header("Location: $url");
}