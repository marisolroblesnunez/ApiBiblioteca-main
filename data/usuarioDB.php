<?php
/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class UsuarioDB {

    private $db;
    private $table = 'usuarios';
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

        //verificar credenciales
        //recibe email y la contraseña y comprueba que sean correctas
        public function verificarCredenciales($email, $password){
            $usuario = $this->getByEmail($email);

            //si no existe el usuario
            if(!$usuario){
                return ['success' => false, 'mensaje' => 'Usuario no encontrado'];
            }
            //verificar si el usuario esta logueado


            if($usuario['bloqueado'] == 1){
                return['success'=> false, 'mensaje' => 'Usuario bloqueado'];
            
            }

            //comprobar la contraseña
            //comprobar contraseña haseada

            if(!$password == $usuario['password']){
                return ['success' =>false, 'mensaje' =>'Contraseña incorrecta'];
            }

            //credenciales son correctas
            //actualizar el ultimo acceso

            //No devolver password, token y token_recuperacion
            unset($usuario['password']);
            unset($usuario['token']);
            unset($usuario['token_recuperacion']);

            return ['success' => true, 'usuario'=> $usuario, 'mensaje' =>'Login correcto'];
        }
       

    }
