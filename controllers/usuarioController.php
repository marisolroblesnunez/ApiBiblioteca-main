<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
//recibe los datos de login y devuelve si son correctos o no


//comprobar que los datos llegan
if(isset($_POST['email']) && isset($_POST['password'])){
    $respuesta = comprobarDatos();
  if($respuesta['error']){//si no es correcto lo volvemos a mandar a login.php
   enviarALogin();
  }else{
    $resultado = consultarBase(); //si es correcto
    if($resultado){
        //enviaral usuario al index
        header("Location: ../admin/index.php");
    }else{
   enviarALogin();
}
  }
  }else{
    enviarALogin();
  }

function enviarALogin(){
header("Location: ../admin/login.php");
exit();
}

function comprobarDatos(){
    $respuesta['error'] = false;
    //limpiar datos
    $email = $_POST['email'];
    $password = $_POST['password'];
    $email = trim($email);
    $email = strtolower($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if(strlen($password) < 8 || strlen($password) > 15){
        $_SESSION['mensaje'] = "La contraseña debe tener entre 8 y 15 caracteres";
        $respuesta['error'] = true;
    }

    return $respuesta;
}

function consultarBase(){
    //consultar si el usuario existe y la contraseña
    return true;
}