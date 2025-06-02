<?php
//recibe los datos de una petición y devuelve una respuesta
class LibroController {
    private $libroDB;
    private $requestMethod;
    private $libroId;

    //el constructor recibe un objeto de la clase LibroDB
    //el método que se ha utilizado en la llamada: GET, POST, PUT o DELETE
    //un id de un libro que puede ser nulo
    public function __construct($db, $requestMethod, $libroId = null)
    {
        $this->libroDB = new LibroDB($db);
        $this->requestMethod = $requestMethod;
        $this->libroId = $libroId;
    }


    public function processRequest(){
        //comprobar si la petición ha sido realizada con GET, POST, PUT, DELETE
        switch($this->requestMethod){
            case 'GET':
                if($this->libroId){
                    //devolver un libro
                    $respuesta = $this->getLibro($this->libroId);
                }else{
                    //libroId es nulo y devuleve todos los libros
                    $respuesta = $this->getAllLibros();
                }
                break;
                
            
            
            case 'POST':
                   
                //crear un nuevo libro
                 $respuesta = $this->createLibro();
                    
                break;

             case 'PUT':
                $respuesta = $this->updateLibro($this->libroId);
                break;   

            case 'DELETE':

                $respuesta = $this->deleteLibro($this->libroId);
                break;
            
            
            default:
                $respuesta = $this->noEncontradoRespuesta();
                break;
        }

            header($respuesta['status_code_header']);
            if($respuesta['body']){
                echo $respuesta['body'];
            }
    }

    private function getAllLibros(){
        //conseguir todos los libros de la tabla libros
        $libros = $this->libroDB->getAll();

        //construir la respuesta
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libros,
            'count' => count($libros)
        ]);
        return $respuesta;
    }

    private function getLibro($id){
        //llamo a la función que devuelve un libro o null
        $libro = $this->libroDB->getById($id);
        //comprobar si $libro es null
        if(!$libro){
            return $this->noEncontradoRespuesta();
        }
        //hay libro
        //construir la respuesta
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libro
        ]);
        return $respuesta;
    }


    private function updateLibro($id){
        //actualizar libro
    }


    private function createLibro(){
        //file_get_contents('php://input') devuelve los datos que vienen en el cuerpo de la petición del cliente
        //se utiliza cuando los datos vienen en formato json
        //json_decode pasa los datos de json a un array asociativo cuando el segundo argumento es true
        //si no le pasamos el segundo argumento, devuelve un objeto

        $input = json_decode(file_get_contents('php://input'), true); 

       if(!$this->validarDatos($input)){
        return $this->datosInvalidosRespuesta();
        //datos no válidos
       }
       $libro = $this->libroDB->create($input);

       if(!$libro){
        return $this->internalServerError();
       }
       //libro creado
       //construir respuesta
               $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libro,
            'message' =>'Libro creado con exito'
        ]);
        return $respuesta;

    }

    
    private function deleteLibro($id){
        $libro = $this->libroDB->getById($id);
        if(!$libro){
            return $this->noEncontradoRespuesta();
        }
        if($this->libroDB->delete($id)){
            //libro borrado
             $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
           'message' => 'Libro eliminado'
        ]);
        return $respuesta;

        }else{
            return $this->internalServerError();
        }
    } ///fin funcion deleteLibro

   
   
   
    private function validarDatos($datos){
         if (!isset($input['titulo']) || !isset($input['autor'])) {
            return false;
        }
            // Validar que fecha_publicacion es un año de 4 dígitos razonable
        $anio = $input['fecha_publicacion'];
        $anioActual = (int)date("Y");

        if (!is_numeric($anio) || strlen((string)$anio) !== 4 || $anio < 1000 || $anio > $anioActual + 1) {
            return false;
        }
        return true;
    }
       
    

    private function noEncontradoRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Libro no encontrado'
        ]);
        return $respuesta;
    }



     private function datosInvalidosRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Datos de entrada inválidos. Se requiere título y autor. La fecha tiene formato (YYYY)' ///el formato (YYYY) se refiere al año 
        ]);
        return $respuesta;
    }

        private function internalServerError(){
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Error interno del servidor'
        ]);
        return $respuesta;
    }
}
