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

    private function noEncontradoRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Libro no encontrado'
        ]);
        return $respuesta;
    }
}