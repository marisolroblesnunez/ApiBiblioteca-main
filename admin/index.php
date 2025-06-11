<?php
//todo comprobar si el usuario esta logueado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control</title>
    <link rel="stylesheet" href="cs/estilos.css">
</head>
<body>
    <div class="container">
<h1>Panel de control</h1>
<div class="panelCrear">
<button id="crear"class="btn-crear">Crear un nuevo libro</button>
</div>
<!-- enctype="multipart/form-data" se utiliza cuando queremos subir archivos-->
<form method= "POST" enctype="multipart/form-data"> 
    <h2> 📚Nuevo libro</h2>
    
    
<div class="form-group">
<label for="titulo">Titulo</label>
<input type="text" id="titulo" name="titulo" required>
<small class="error" id="error-titulo"></small>
</div>

<div class="form-group">
<label for="autor">Autor</label>
<input type="text" id="autor" name="autor" required>
<small class="error" id="error-autor"></small>
</div>
<div class="form-group">
<label for="genero">Género</label>
<input type="text" id="genero" name="genero">
</div>
<div class= form-group>
<label for="fecha_publicacion">Fecha de publicación</label>
<input type="number" id="fecha_publicacion" name="fecha_publicacion" min="1000">
<small class="error" id="error-publicacion"></small>
</div>
<div class= form-group>
<label for="imagen">Imagen</label>
<input type="file" id="imagen" name="img" accept="image/*">
<small class="error" id="error-imagen"></small>
</div>
<div class="checkbox-group">
<input type="checkbox" id="disponible" name="disponible">
<label for ="disponible">Disponible</label>
</div>

<div class="checkbox-group">
<input type="checkbox" id="favorito" name="favorito">
<label for="favorito">Favorito</label>
</div>

<div class="form-group">
<label for="resumen">Resumen</label>
<textarea name="resumen" id="resumen" rows="6" placeholder="Escribe un brebe resumen del libro..."></textarea> 
<small class="error" id="error-resumen" ></small>
</div>


<button type="submit" id="btnGuardar">Guardar libro</button>
</form>
<table class="tablaLibros" id="tablaLibros"></table>
 </div>
<!-- 
 <div id="modal" class="modal hidden">
      <div class="modal-contenido">
        <span class="cerrar">&times;</span>
        <img id="modal-imagen"  alt="Portada libro">
        <p id="modal-info"></p>
      </div>
    </div> -->
    
   <script src="js/funciones.js"></script>
</body>
</html>