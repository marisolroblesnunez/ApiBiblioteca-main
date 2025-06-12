<?php
//recibe los datos de una petición y devuelve una respuesta //////////////ESTA CARPETA LO QUE HACE ES COMUNICARSE CON LA BASE DE DATOS Y DECIRLE TODO LO QUE QUEREMOS QUE PUEDA CAMBIAR EL CLIENTE////////////////////////////////
class LibroController {

    //estas son las propiedades de la clase lo que necesito
    private $libroDB;//contactar con la base de datos,, esto es una estancia de database
    private $requestMethod;// el metodo de llamada
    private $libroId;//para que seleccione el id

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


        //Comprobar si viene la clave_method en el objeto
        $metodo = $this->requestMethod;
        if($this->requestMethod === 'POST' && isset($_POST['_method'])){
            $metodo = strtoupper($_POST['_method']); /////aqui le digo que le diga a la base de datos que el metodo es put, porque en javascript en la parte donde digo if(modo edicion) y se lo añado como hijo a formData lo del put, (ahora se lo tengo que decir tambien a la base de datos y se lo digo asi para  por si a caso tuviera que cambiar el metodo)
        }

        //comprobar si la petición ha sido realizada con GET, POST, PUT, DELETE
        switch($metodo){    //////esto es como un if/else pero de muchos casos en especifico, entonces se utiliza mejor el swich,,, y al final se pone default porque es commo si pusieramos y si no esto (else if)
           
            case 'GET':
                //BUSCAR un libro
                if($this->libroId){
                    //devolver un libro
                    $respuesta = $this->getLibro($this->libroId);
                }else{
                    //libroId es nulo y devuleve todos los libros
                    $respuesta = $this->getAllLibros();
                }
                break;
                
            
            
            case 'POST':
                   //CREAR un nuevo libro
                 $respuesta = $this->createLibro();
                    
                break;

           
                case 'PUT':
                //MODIFICAR un libro
                $respuesta = $this->updateLibro($this->libroId);
                break;   

            
                case 'DELETE':
                //BORRAR un libro
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
        $libro = $this->libroDB->getById($id);
        if(!$libro){
            return $this->noEncontradoRespuesta();
        }

        //el libro existe
        //verificar si los datos vienen en $_POST con FormData y method spoofing o en el body
        if(!empty($_POST['datos'])){
            $input = json_decode($_POST['datos'], true);
        }else{
            //leo los datos que llegan en el body de la peticion
            $input = json_decode(file_get_contents('php://input'),true);}

        
       //validar datos
        if(!$this->validarDatos($input)){
            return $this->datosInvalidosRespuesta();
        }
        //el libro existe y los datos que llegan son validos

        //guardar el nombre de la imagen actual
        $nombreImagenAnterior = $libro['img'];
        $nombreImagenNueva = $nombreImagenAnterior;

        //procesar la imagen si viene

        ////files sirve solo para subir archivos, en este caso un archivo de imagen
            //si la imagen existe y tiene algun error le dices que hay error (esto no se realmente si es asi pero es lo que creo)
        if(isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK){
            //Se ha subido un archivo y se ha subido sin errores
            $validacionImagen = $this->validarImagen($_FILES['img']);
            
            ///si no se a subido bien pues le digo esto:
            if(!$validacionImagen['valida']){
                return $this->imagenInvalidaRespuesta($validacionImagen['mensaje']);
            }

            //guardar la nueva imagen con nombre basado en el título
            $nombreImagenNueva = $this->guardarImagen($_FILES['img'], $input['titulo']);
            if(!$nombreImagenNueva){
                return $this->errorGuardarImagenRespuesta();
            }


        }

        $input['img'] = $nombreImagenNueva;
        $libroActualizado = $this->libroDB->update($this->libroId, $input);
        
        if(!$libroActualizado){
            return $this->internalServerError();
        }
        //el libro se a actualizado con éxito
        //construyo la respuesta
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
         $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libroActualizado,
            'mensaje' =>'Libro actualizado con exito'
        ]);
        return $respuesta;


    }


    private function createLibro(){
        //file_get_contents('php://input') devuelve los datos que vienen en el cuerpo de la petición del cliente
        //se utiliza cuando los datos vienen en formato json
        //json_decode pasa los datos de json a un array asociativo cuando el segundo argumento es true
        //si no le pasamos el segundo argumento, devuelve un objeto



        ////verificar como vienen los datos: en el body (JSON) o en el array $_POST (formData)
        if(!empty($_POST['datos'])){
            //los datos vienen en formData y puede que venga un archivo
            $input = json_decode($_POST['datos'], true);
        }else{
            //los datos vienen en el JSON en el body
            $input = json_decode(file_get_contents('php://input'), true); 
        }

        if(!$this->validarDatos($input)){
           
            return $this->datosInvalidosRespuesta();
            //datos no válidos
        }

       //comprobar si vienen la imagen y procesarla
       $nombreImagen = '';
       if(isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK){ //UPLOAD ERR OK SIGNIFICA QUE NO HAY ERROR AL SUBIR EL ARCHIVO
           
        ////VALIDAR IMAGEN
        $validacionImagen = $this->validarImagen($_FILES['img']);

        if(!$validacionImagen['valida']){
            //la imagen no ha pasado la validacion
            return $this->imagenInvalidaRespuesta($validacionImagen['mensaje']);
        }
    
        //viene un archivo y es una imagen válida
        //guardar imagen en el servidor con nombre basado en el título
        $nombreImagen = $this->guardarImagen($_FILES['img'], $input['titulo']);
        if(!$nombreImagen){
            return $this->errorGuardarImagenRespuesta();
        }

       }//fin de comprobacion de si viene una imagen


       //añadir el nombre de la imagen a los datos del nuevo libro
       if($nombreImagen !== false){
        $input['img'] = $nombreImagen;
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
            'mensaje' =>'Libro creado con exito'
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
           'mensaje' => 'Libro eliminado'
        ]);
        return $respuesta;

        }else{
            return $this->internalServerError();
        }
    } ///fin funcion deleteLibro


    private function guardarImagen($archivo, $titulo){
        //Limpiar el titulo para utilizarlo como nombre de archivo
        $nombreLimpio = $this->limpiarNombreArchivo($titulo);

        //obtener la extension del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        //Crear el nombre del archivo
        $nombreArchivo = $nombreLimpio .  '.' .$extension;

        //definir rutas
        $directorDestino = '../img/imgPequenias/';
        $rutaCompleta = $directorDestino . $nombreArchivo;

        //crear el directorio si no existe
        if(!file_exists($directorDestino)){
            mkdir($directorDestino, 0755,true);
        }

        //movemos el archivo subido
        if(move_uploaded_file($archivo['tmp_name'], $rutaCompleta)){
            return $nombreArchivo;
        }
        return false;
    }


////esta funcion la hago para que cuando el cliente,() el dueño al que le voy a vender esto) borre una imagen y se me borre tambien de la programacion esta que es la que se conecta de verdad con la base de datos,para que desaparezca la imagen de el fichero de imgPequenias y asi solo esten las imágenes de los libros que esten guardados, de los que el dueño vaya borrando en la aplicacion que le hemos hecho pues que se nos borren de aqui tambien, ASI SOLO TENEMOS LAS IMAGENES DE LOS LIBROS GUARDADOS Y DE LOS ELIMINADOS PUES TAMBIEN SE BORRA LA IMAGEN DEL LIBRO BORRADO AQUI.
    private function eliminarImagen($nombreArchivo){
        if(empty($nombreArchivo)) return;
        $rutaArchivo = "../img/imgPequenias/" . $nombreArchivo;
        if(file_exists($rutaArchivo)){
            unlink($rutaArchivo);
        }
    }

   
   
   
    private function validarDatos($datos){
         if (!isset($datos['titulo']) || !isset($datos['autor'])) {
            return false;
        }
            // Validar que fecha_publicacion es un año de 4 dígitos razonable
        $anio = $datos['fecha_publicacion'];
        $anioActual = (int)date("Y");

        if (!is_numeric($anio) || strlen((string)$anio) !== 4 || $anio < 1000 || $anio > $anioActual + 1) {
            return false;
        }
        return true;
    }
       

    private function validarImagen($archivo){
        //validar que el archivo recibido sea una imagen válida

        //verificando errores de subida
        if($archivo['error'] !== UPLOAD_ERR_OK){
            return['valida' => false, 'mensaje' => "Error al subir el archivo"];
        }

        //verificar el tamaño del archibo (1MB máximo)
        $tamanioMaximo = 1024 * 1024;
        if($archivo['size'] > $tamanioMaximo){
            return ['valida' => false, 'mensaje' => "La imagen no puede superar 1MB"];
        }

        //verificar tipo MIME
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'imagen/gif', 'image/webp'];
        if(!in_array($archivo['type'], $tiposPermitidos)){
            return ['valida' => false, 'mensaje' => "Solo se permiten imágenes JPEG, PNG, GIF, WEBP"];
        }
        //Verificar la extension del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if(!in_array($extension, $extensionesPermitidas)){
            return ['valida' => false, 'mensaje' => "Extension del archivo no permitida"];
        }
        
        //verificar que realmente sea una imagen
        $infoImagen = getimagesize($archivo['tmp_name']);
        if($infoImagen === false){
            return ['valida' => false, 'mensaje' => "El archivo no es una imagen válida"];
        }
        return ['valida' => true, 'mensaje' => ""];
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

    private function imagenInvalidaRespuesta($mensaje){
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Imagen inválida' . $mensaje
        ]);
        return $respuesta;
    }


       private function errorGuardarImagenRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Error al guardar la imagen en el servidor'
        ]);
        return $respuesta;
    }
 
 
 
    private function limpiarNombreArchivo($titulo) {
        // Convertir a minúsculas
        $nombre = strtolower($titulo);
        
        // Reemplazar caracteres especiales y espacios
        $nombre = preg_replace('/[áàäâ]/u', 'a', $nombre);
        $nombre = preg_replace('/[éèëê]/u', 'e', $nombre);
        $nombre = preg_replace('/[íìïî]/u', 'i', $nombre);
        $nombre = preg_replace('/[óòöô]/u', 'o', $nombre);
        $nombre = preg_replace('/[úùüû]/u', 'u', $nombre);
        $nombre = preg_replace('/[ñ]/u', 'n', $nombre);
        $nombre = preg_replace('/[ç]/u', 'c', $nombre);
        
        // Reemplazar espacios y caracteres no alfanuméricos con guiones bajos
        $nombre = preg_replace('/[^a-z0-9]/i', '_', $nombre);
        
        // Eliminar guiones bajos múltiples
        $nombre = preg_replace('/_+/', '_', $nombre);
        
        // Eliminar guiones bajos al inicio y final
        $nombre = trim($nombre, '_');
        
        // Limitar longitud
        if (strlen($nombre) > 50) {
            $nombre = substr($nombre, 0, 50);
            $nombre = trim($nombre, '_');
        }
        
        return $nombre ?: 'libro_sin_titulo';
    }

}
