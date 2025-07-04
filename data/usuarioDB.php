<?php
/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class UsuarioDB {

    private $db;
    private $table = 'usuarios';
    private $url = 'http://localhost/ApiBiblioteca-MAIN/admin';
    //recibe una conexión ($database) a una base de datos y la mete en $db
    public function __construct($database){
        $this->db = $database->getConexion();
    }

    //extrae todos los datos de la tabla $table
    public  function getAll(){
        //construye la consulta
        $sql = "SELECT * FROM {$this->table}";

        //realiza la consulta con la función query()
        $resultado = $this->db->query($sql);

        //comprueba si hay respuesta ($resultado) y si la respuesta viene con datos
        if($resultado && $resultado->num_rows > 0){
            //crea un array para guardar los datos
            $usuarios = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $usuarios[] = $row;
            }
            //devolvemos el resultado
            return $usuarios;
        }else{
            //no hay datos, devolvemos un array vacío
            return [];
        }
        
    }

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if($stmt){
            //añado un parámetro a la consulta
            //este va en el lugar de la ? en la variable $sql
            //"i" es para asegurarnos de que el parámetro es un número entero
            $stmt->bind_param("i", $id);
            //ejecuta la consulta
            $stmt->execute();
            //lee el resultado de la consulta
            $result = $stmt->get_result();

            //comprueba si en el resultado hay datos o está vacío
            if($result->num_rows > 0){
                //devuelve un array asociativo con los datos
                return $result->fetch_assoc();
            }
            //cierra 
            $stmt->close();
        }
        //algo falló
        return null;
    }
   
   
            //eliminar un libro
        public function delete($id){
            $sql = "DELETE FROM {$this->table} Where id = ? ";
            $stmt = $this->db->prepare($sql);
            if($stmt){
                $stmt->bind_param("i", $id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
            return false;
        }

        //buscar un usuario por su email
        //si existe devuelve sus datos y si no existe devuelve null
        public function getByEmail($email){
            $sql = "SELECT * FROM {$this->table} where email = ?";
            $stmt = $this->db->prepare($sql);
            if($stmt){
                $stmt->bind_param("s",$email);
                $stmt->execute();
                $result = $stmt->get_result(); 
                
                //comprobar si hay un usuario en $result
            if($result->num_rows > 0){
                //el usuario existe
                $usuario = $result->fetch_assoc();
                $stmt->close();
                return $usuario;
                }
            $stmt->close();
             }
            return null;
            
        }
        /**
         *crear un nuevo usuario
         */
        
         public function registrarUsuario($email, $password, $verificado = 0){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $token = $this->generarToken();

        //comprobar si el email existe
        $existe = $this->correoExiste($email);

        $sql = "INSERT INTO usuarios (email, password, token, bloqueado) VALUES(?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $email, $password, $token, $verificado);

        if(!$existe){
            if($stmt->execute()){
                // correcto
                $mensaje = "Por favor, verifica tu cuenta haciendo clic en este enlace: $this->url/verificar.php?token=$token";
                //$mensaje = Correo::enviarCorreo($email, "Cliente", "Verificación de cuenta", $mensaje);
                $mensaje = $this->enviarCorreoSimulado($email, "Verificación de cuenta", $mensaje);
            }else{
                $mensaje = ["success" => false, "mensaje" => "Error en el registro: " . $stmt->error];
            }
        }else{
            $mensaje = ["success" => false, "mensaje" => "Ya existe una cuenta con ese email"];
        }

        return $mensaje;
    }



public function generarToken(){
    return bin2hex(random_bytes(32));
}

 public function correoExiste($correo, $excludeId = null){
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
        $params = [$correo];
        $types = "s";

        if($excludeId){
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= "i";
        }

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
            return $exists;
        }
        return false;
    }

        //funcion para enviar correo simulado
    public function enviarCorreoSimulado($destinatario, $asunto, $mensaje){
        $archivo_log = __DIR__ . '/correos_simulados.log';
        $contenido = "Fecha: " . date('Y-m-d H:i:s'. "\n");
        $contenido .= "Para: $destinatario\n";
        $contenido .= "Asunto: $asunto\n";
        $contenido .= "Mensaje:\n$mensaje\n";
        $contenido .= "__________________________________________\n\n";

        file_put_contents($archivo_log, $contenido, FILE_APPEND);

        return ["success" => true, "mensaje" => "Registro exitoso. Por favor, verifica tu correo"];
    }



        //verificar credenciales
        //recibe email y la contraseña y comprueba que sean correctas
        public function verificarCredenciales($email, $password){
            $usuario = $this->getByEmail($email);

            //si no existe el usuario
            if(!$usuario){
                return ['success' => false, 'mensaje' => 'Usuario no encontrado'];
            }


            //verificar que el usuario esta bloqueado 
            if($usuario['bloqueado'] == 1){
                return['success'=> false, 'mensaje' => 'Usuario bloqueado'];
            
            }
             //comprobar que el usuario ha verificado el email
        if($usuario['verificado'] === 0){
            return ['success' => false, 'mensaje' => 'Verifica tu correo'];
        }

            //comprobar la contraseña
            //comprobar contraseña haseada

            if(!password_verify($password, $usuario['password'])){
                return ['success' =>false, 'mensaje' =>'Contraseña incorrecta'];
            }

            //credenciales son correctas - actualizar el ultimo acceso
            $this->actualizarUltimoAcceso($usuario['id']);
           

            //No devolver password, token y token_recuperacion
            unset($usuario['password']);
            unset($usuario['token']);
            unset($usuario['token_recuperacion']);

            return ['success' => true, 'usuario'=> $usuario, 'mensaje' =>'Login correcto'];
        }
       public function actualizarUltimoAcceso($id){
        $sql = "UPDATE {$this->table} SET ultima_conexion = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        if($stmt){
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
       }


        public function verificarToken($token){
        //buscar al usuario con el token recibido
        $sql = "SELECT id FROM usuarios WHERE token = ? AND verificado = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            //token es válido actualizamos el estado de verificación del usuario
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            
            $update_sql = "UPDATE usuarios SET verificado = 1, token = NULL WHERE id= ?";
            $update_stmt = $this->db->prepare($update_sql);
            $update_stmt->bind_param("i", $user_id);

            $resultado = ["success" => false, "mensaje" => "Hubo un error al verificar tu cuenta. Por favor, intenta de nuevo más tarde"];

            if($update_stmt->execute()){
                $resultado = ["success" => true, "mensaje" => "Tu cuenta ha sido verificada. Ahora puedes iniciar sesión"];
            }
            
        }else{
            $resultado = ["success" => false, "mensaje" => "Token no válido"];
        }
        return $resultado;
    }    

     public function recuperarPassword($email){

        $existe = $this->correoExiste($email);

        $resultado = ["success" => false, "mensaje" => "El correo electrónico  proporcionado no corresponde a ningún usuario registrado."];

        //si el correo existe en la bbdd
        if($existe){
            $token = $this->generarToken();

            $sql = "UPDATE usuarios SET token_recuperacion = ? WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $token, $email);

            //ejecuta la consulta
            if($stmt->execute()){
                $mensaje = "Para restablecer tu contraseña, haz click en este enlace: $this->url/restablecer.php?token=$token";
                //$mensaje = Correo::enviarCorreo($email, "Cliente", "Restablecer Contraseña", $mensaje);
                $this->enviarCorreoSimulado($email, "Recuperación de contraseña", $mensaje);
                $resultado = ["success" => true, "mensaje" => "Se ha enviado un enlace de recuperación a tu correo"];
            }else{
                $resultado = ["success" => false, "mensaje" => "Error al procesar la solicitud"];
            }
        }
        return $resultado;
    }

    public function restablecerPassword($token, $nueva_password){
        $password = password_hash($nueva_password, PASSWORD_DEFAULT);
        //buscamos al usuario con el token proporcionado
        $sql = "SELECT id FROM usuarios WHERE token_recuperacion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        $resultado = ["success" => false, "mensaje" => "El token de recuperación no es válido o ya  ha sido utilizado"];

        if($result->num_rows === 1){
            $row = $result->fetch_assoc();
            $user_id = $row['id'];

            //actualizar la contraseña y eliminar el token de recuperación
            $update_sql = "UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE id = ?";
            $update_stmt = $this->db->prepare($update_sql);
            $update_stmt->bind_param("si", $password, $user_id);

            if($update_stmt->execute()){
                $resultado = ["success" => true, "mensaje" => "Tu contraseña ha sido actualizada correctamente"];
            }else{
                $resultado = ["success" => false, "mensaje" => "Hubo  un error al actualizar tu contraseña. Por favor, intenta de nuevo más tarde"];
            }
        }
        return $resultado;
    }


    }
