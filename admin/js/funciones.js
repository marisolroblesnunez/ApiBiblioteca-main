//cuando hicimos la api del tiempo hicimos esto, lo puedo coger de ejemplo también

//const url = 'http://localhost/ApiBiblioteca-MAIN/api/libros';
const url = 'http://localhost/ApiBiblioteca-MAIN/api/libros';

let librosData = [] //almacenar los datos de todos los libros
let modoEdicion = false //para saber si estamos creando o editandoo
let libroEditandoId = null //ID del libro que se está editando
document.addEventListener('DOMContentLoaded', ()=>{

    
  // const divLibros = document.getElementById('divLibros');
  // const modal      = document.getElementById('modal');
  // const modalImg   = document.getElementById('modal-imagen');
  // const modalInfo  = document.getElementById('modal-info');
  // const cerrarBtn  = document.querySelector('.cerrar');

  let librosGlobal = [];

    
    //realizo la llamada a la api para conseguir los datos
    fetch(url)
    .then (response => response.json()) //la guardo en la variable response y la transformo en json
    .then(data => mostrarLibros(data))
    .catch(error => console.error('Error:', error));
document.getElementById("crear").addEventListener('click', () => {

  //si document.querySelector('form').style.display devuelve un valor vacío
  //estado toma el segundo valor none
  const estado = document.querySelector('form').style.display || 'none';
  console.log(estado)
  if(estado === 'none'){

    document.querySelector('form').style.display = 'grid' //block
    document.getElementById("crear").textContent = 'Ocultar formulario'
  
  }else{
    document.querySelector('form').style.display = 'none'
    document.getElementById("crear").textContent = 'Crear nuevo libro'
  }

  if(modoEdicion){
    resetearModoCreacion()
  }
})
document.querySelector('form').addEventListener('submit', enviarDatosNuevoLibro)
})


 function mostrarLibros(datos) {
const libros = datos.data;
librosData = libros
console.log(libros)


    let tablaLibros = document.getElementById('tablaLibros');
    tablaLibros.innerHTML = '';
    librosGlobal = datos.data;   // guardamos globalmente
if (datos.success && datos.count > 0) {
    document.getElementById('tablaLibros').innerHTML = 
     "<tr class='cabeceras'>" +
     Object.keys(librosGlobal[0]).map(clave =>
        `
        <td>${clave.toUpperCase()}</td>
        ${
          clave == 'resumen' ? '<td colspan="2">Acciones</td>' : ''
        }
        
        `
     ).join('')
     +"</tr>";

      document.getElementById('tablaLibros').innerHTML += librosGlobal.map(libro => `
       <tr>
        <td>${libro.id}</td>
        <td>${libro.titulo}</td>
        <td>${libro.autor}</td>
        <td>${libro.genero}</td>
        <td class="centrado">${libro.fecha_publicacion}</td>
        <td class="centrado">${(libro.disponible == 1) ? "Sí" : "No"}</td>
        <td>${(libro.img && libro.img.trim() !== '') ? `<img src="../img/imgPequenias/${libro.img}?${new Date().getTime()}" alt="${libro.titulo}"/>` : 'Sin Imagen'}</td>
        <td class="centrado">${(libro.favorito == 1) ? "Sí" : "No"}</td>
        <td>${(libro.resumen !== null && libro.resumen.length > 0) ? libro.resumen.substring(0, 100)+"..." : ''}</td>
        <td><button onclick="editarLibro(${libro.id})">Editar</button></td>
        <td><button onclick="eliminarLibro(${libro.id}, '${libro.titulo}')" class="btn-delete">Eliminar</button></td>
        </tr>
      `).join('')

      // Ahora enlazamos clic a cada .libro
      document.querySelectorAll('.libro').forEach((el, idx) => {
       el.addEventListener('click', () => openModal(librosGlobal[idx]));
      });
    } else if (datos.count === 0) {
      tablaLibros.innerHTML = "<p>No hay libros</p>";
    }
  }



  function eliminarLibro(id, titulo){

  const confirma = confirm(`¿seguro que quieres eliminar el libro: ${titulo}?`)
  if(!confirma){
    return
  }
  //El usuario a confirmado que quiere eliminar el libro
  fetch(`${url}/${id}`, {
    method: 'DELETE'
  })
  .then(response =>response.json())
  .then(data => libroEliminado(data))
  .catch(error => console.error('Error:', error));
}

function libroEliminado(data){
  if(data.success){
    fetch(url)
    .then(response =>response.json())
  .then(data => mostrarLibros(data))
  .catch(error => console.error('Error:', error));
  }else{
    alert("Hubo un problema al eliminar")
  }
}



function editarLibro(id){

  //ir al inicio de la página

  window.scrollTo({top: 0, behavior: 'smooth'})

  //buscamos el libro que queremos modificar
  const libro = librosData.find(lib => lib.id == id)

  if(libro){
    //activar el modo edicion
    modoEdicion = true
    libroEditandoId = id

    //rellenamos el formulario con los datos del libro que queremos editar
    rellenarFormularioEdicion(libro) 

    //mostramos formulario en modo edicion
    mostrarFormularioEdicion()
  }else{
    alert("Error: No se encontraron los datos del libro")
  }
}

function rellenarFormularioEdicion(libro){
document.getElementById('titulo').value = libro.titulo || ''
document.getElementById('autor').value = libro.autor || ''
document.getElementById('genero').value = libro.genero || ''
document.getElementById('fecha_publicacion').value = libro.fecha_publicacion || ''
document.getElementById('disponible').checked = libro.disponible ==1
document.getElementById('favorito').checked = libro.favorito ==1
document.getElementById('resumen').value = libro.resumen || ''


//limpiar el campo de la imagen
document.getElementById('imagen').value = ''

//mostrar la imagen actual si existe
mostrarImagenActual(libro.img, libro.titulo)


}


function mostrarFormularioEdicion(){

  //Mostrar formulario
  document.querySelector('form').style.display = 'grid'

  //cambiar los textos para el modo edicion
  document.querySelector('form h2').textContent = "Editar libro"
  document.getElementById('btnGuardar').textContent = "Actualizar libro"
  document.getElementById("crear").textContent = "Ocultar formulario"

}

function mostrarImagenActual(imagen, titulo){
//Eliminar imagen previa si existe
  const imagenPrevia = document.getElementById('imagen-actual')
  if(imagenPrevia){
    imagenPrevia.remove()
  }

  //comprobar que el libro tiene imagen
  if(imagen && imagen.trim() !== ''){
    //crear un elemento para mostrar la imagen actual
    const divImagen = document.createElement('div')
    divImagen.id = 'imagen-actual'
    divImagen.innerHTML = `
    <p><strong>Imagen actual</strong></p>
    <img class="imagenEditar" src="../img/imgPequenias/${imagen}?${new Date().getTime()}" alt="${titulo}" />
    <p>Selecciona una nueva imagen para reemplazarla</p>
    `

    //Mostrar el divImagen despues del input de imagen
    const inputImagen = document.getElementById('imagen')
    inputImagen.before(divImagen)
  }
}

function enviarDatosNuevoLibro(e){
  e.preventDefault();//con esta funcion significa que se para el envio del formulario
//PARA LEER LOSO DATOS DEL FORMULARIO
  const mensajesError = document.querySelectorAll('.error') //.error porque estoy 
  const titulo = document.getElementById('titulo').value.trim()
  const autor = document.getElementById('autor').value.trim()
  const genero = document.getElementById('genero').value.trim()
  const fecha_publicacion = parseInt(document.getElementById('fecha_publicacion').value)
  const imagen = document.getElementById('imagen').files[0]
  const disponible = document.getElementById('disponible').checked
  const favorito = document.getElementById('favorito').checked
  const resumen = document.getElementById('resumen').value.trim()
 
 
  //COMPROBAR QUE LOS DATOS ESTEN BIEN
 
  //limpiar mensajes de error previos
  mensajesError.forEach(elemento => elemento.textContent = '')

  let errores = false
 // realizar las validaciones
 if(!titulo){
  document.getElementById('error-titulo').textContent = "El titulo es obligatorio"
  errores = true
 }

 if(!autor){
  document.getElementById('error-autor').textContent = "El autor es obligatorio"
  errores = true 
 }
 const anioActual = new Date().getFullYear();
 if(isNaN(fecha_publicacion) || fecha_publicacion < 1000 || fecha_publicacion > anioActual + 1){
  document.getElementById('error-publicacion').textContent = "La fecha de publicación debe ser un año valido (4 dígitos)."
  errores = true
 }

 if(resumen.length > 1000){
  document.getElementById('error-resumen').textContent = "El resumen no puede superar los 1000 caracteres."
  errores = true
 }
//comprobar el archivo de imagen
 if(imagen){
  const validacionImagen = validarImagen(imagen)
  if(!validacionImagen.esValido){
    document.getElementById('error-imagen').textContent = validacionImagen.mensaje // la funcion validarImagen devuelve un objeto con esValido y con mensaje, entonces aqui hago referencia al mensaje poniendo .mensaje (me refiero al objeto mensaje de la funcion validarImagen)
    errores = true 
  }

 }
//si no ponemos el nombre de la clave, se crea una con el mismo nombre de la variable
 if(errores) return //si hay errores no se envia el formulario
 const datos = {
  titulo,
  autor,
  genero,
  fecha_publicacion,
  disponible,
  favorito,
  resumen
 }
console.log(datos);
 const formData = new FormData();//es un tipo de objeto que nos deja para poder subir el archivo  y los datos al servidor (al librocontroler)
 formData.append("datos", JSON.stringify(datos)) // el apend es para meterle cosas dentro, aqui le meto los datos

 if(imagen){
  formData.append("img", imagen)   /////////////aqui le meto el archivo
 }

 //////////////ESTO ES PARA PODER MODIFICAR EL LIBRO!!!!!!!!/////////////////////////
 
 const metodo = 'POST' //////Deberia de ser put pero al tener una imagen (archivo), pues no se puede poner put, porque da error, entonces ponemos POST y luego le decimos que realmente es put porque lo que queremos es modificar un libro

 const urlPeticion = modoEdicion ? `${url}/${libroEditandoId}` : url

 ////////////////////SI LO ACTUALIZO///ME PONES ESTO////////////Y SI NO//////ME PONES ESTO
 const mensajeExito = modoEdicion ? "Libro actualizado con éxito" : "Libro guardado con éxito"
//si estamos en modo edicion añadimos un parametro _method
 if(modoEdicion){
  formData.append("_method", "PUT") ////aqui le añadimos a formData que el metodo para actualizar realmente es put
 }

 fetch(urlPeticion, {
    method: metodo, ////tengo que poner esto porque si no por defecto el metodo seria get
    body: formData    ///aqui van los datos y el archivo, porque se lo he metido arriba
 })
 .then(response =>{
    console.log(response)
    return response.json()
})

.then(data => {
    console.log("data.succes = "+data.success);
    if(data.success){
      alert(mensajeExito)
      //quito todods los datos del formulario que haya metidos
      document.querySelector('form').reset()
      //oculto el formulario
      document.querySelector('form').style.display = "none"
      //cambio el texto del boton
      document.getElementById("crear").textContent = "Crear un nuevo libro"
      resetearModoCreacion()
      
      //volvemos a pedir todos los libros
      cargarLibros()

    }else{
      alert("Oppss ocurrió algún error" , data.error)
    }
 })
 .catch(error => {
  console.error("Error al enviar datos: ", error)
  const accion = modoEdicion ? "actualizar" : "guardar"
  alert(`Error al ${accion} el libro`)
 })
}



function cargarLibros(){
  fetch(url)
  .then(response => response.json())
 .then(data => mostrarLibros(data))
 .catch(error => console.log('Error: ', error));
 

}


function validarImagen(archivo){
  console.log('Archivo tipo: ' , archivo.type)
  console.log ('tamaño del archivo: ', archivo.size)//la coma es para que me salgan dos argumentos
  //si no hay archivo pasa la validación

  if(!archivo){
    return {
      esValido: true, 
      mensaje: ""}}

    //validar el tipo de archivo

    const tiposPermitidos = ['image/jpeg' , 'image/jpg', 'image/png', 'image/gif', 'image/webp']
    if(!tiposPermitidos.includes(archivo.type)){
      return {
        esValido: false,
        mensaje: "solo se admiten archivos de imgagen (JPEG, JPG, PNG, GIF, WEBP)"
      }
    }

   //validar el tamaño del archivo
   const tamanioMaximo = 1024 * 1024 //esto se pone porque un mega son 1024 por 1024.
   const tamanioMaximoMB = 1
   if(archivo.size > tamanioMaximo){
    return {
      esValido: false,
      mensaje: `La imagen no puede superar los ${tamanioMaximoMB} MB. Tamaño actual: ${(archivo.size / (1024 * 1024)).toFixed(2)} MB.`
    }
   }

   //comprobar que el archivo tenga contenido

   const tamanioMinimo = 1024
   if(archivo.size < tamanioMinimo){
    return {
      esValido: false,
      mensaje: "El archivo de la imagen está vacío o es demasiado pequeño"
    }
   }
   return{
    esValido: true,
    mensaje: ''
   }

}


//      function mostrarLibros(datos) {
//     librosGlobal = datos.data;   // guardamos globalmente
//     if (datos.success && datos.count > 0) {
//       divLibros.innerHTML = librosGlobal.map(libro => `
//         <div class="libro">
//           <img src="img/imgPequenias/${libro.img}" alt="${libro.titulo}">
//           <h3>${libro.titulo}</h3>
//           <p>${libro.resumen}</p>
//         </div>
//       `).join('');

//       // Ahora enlazamos clic a cada .libro
//       document.querySelectorAll('.libro').forEach((el, idx) => {
//         el.addEventListener('click', () => openModal(librosGlobal[idx]));
//       });

//     } else if (datos.count === 0) {
//       divLibros.innerHTML = "<p>No hay libros</p>";
//     }
//   }





// function openModal(libro) {
//     console.log(libro)
//     modalImg.src = `img/${libro.img}`; 
//     modalImg.alt = libro.titulo;
//     modalInfo.innerHTML = `
//       <strong>Género:</strong> ${libro.genero}<br>
//       <strong>Publicado:</strong> ${libro.fecha_publicacion}<br>
//       <strong>Disponible:</strong> ${libro.disponible ? 'Sí' : 'No'}
//     `;
//     modal.classList.remove('hidden');
//   }

//   cerrarBtn.addEventListener('click', () => {
//     modal.classList.add('hidden');
//   });

//   // Cerrar al hacer clic fuera del contenido
//   modal.addEventListener('click', e => {
//     if (e.target === modal) modal.classList.add('hidden');
//   });


  function resetearModoCreacion(){
    modoEdicion = false
    libroEditandoId = null
     

  //restauramos los textos originales
  document.querySelector('form h2').textContent = "Nuevo Libro"
  document.getElementById('btnGuardar').textContent = 'Guardar Libro'

  //Eliminar la imagen actual si existe
  const imagenPrevia = document.getElementById('imagen-actual')
  if(imagenPrevia){
    imagenPrevia.remove()
  }
  document.querySelector('form').reset()
   }