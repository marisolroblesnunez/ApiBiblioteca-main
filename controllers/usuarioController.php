<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}



//recibe los datos de login y devuelve si son correctos o no

//////UNA MANERA DE HACERLO:

//comprobar que los datos llegan
// if(isset($_POST['email']) && isset($_POST['password'])){
//     $respuesta = comprobarDatos();
//   if($respuesta['error']){//si no es correcto lo volvemos a mandar a login.php
  //  enviarALogin();
  // }else{
  //   $resultado = consultarBase(); //si es correcto
    // if($resultado){
        //enviaral usuario al index
//         header("Location: ../admin/index.php");
//     }else{
//    enviarALogin();
// }
//   }
//   }else{
//     enviarALogin();
//   }


/////////////OTRA MANERA DE HACERLO: 

//para comprobar que los datos sean correctos
////si no existe el email o no existe el paswor- enviamelo otra vez a login que es la carpeta donde voy a decirle que rellene el formulario.
if(!isset($_POST['email']) || !isset($_POST['password'])){
  enviarALogin();
  return;
}

//comprobar que los datos sean correctos


////si la respuesta es incorrecta, hay algún error-  enviamelo otra vez a login 
$respuesta = comprobarDatos();
if($respuesta['error']){
  enviarALogin();
  return;
}

//comprobar si el usuario existe en la base DE DATOS para comprobar que ssea verdad que esta logueado y si es verdad pues lo redirige a la carpeta index que es la que puede entrar en la libreria

$resultado = consultarBase();
if(!$resultado){
  enviarALogin(); 
  return; }

//enviar al usuario al index
header("Location: ../admin/index.php"); //////header(Location: //se utiliza para redirigir a la carpeta que pongamos en el parentesis se pone header(Location: y a continuacion la carpeta donde queremos que redirija, en este caso ../admin/index.php)

//////$_SESSION es un array asociativo que se puede utilien el que se mete la informacion que quieras, aqui meto informacion para despues utilizarla desde ootra carpeta , por eso es superglobal porque es una variable con informacion que se puede leeer desde cualquier sitio, aunque si lo voy a utilizar desde otra carpeta, tengo que poner primero:
  //if(SESSION_STATUS()== php sesion_none) esto lo tengo reflejado en la carpeta login al princcipio, porque quiero utilizar estas globales,,,,, si las quiero utilizar en esta misma carpeta, no me hace falta poner nada mas
$_SESSION['mensaje'] = "Se ha logueado correctamente";
$_SESSION['nombre'] = $usuario;
$_SESSION['logueado'] = true;

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