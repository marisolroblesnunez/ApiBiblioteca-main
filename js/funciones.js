//cuando hicimos la api del tiempo hicimos esto, lo puedo coger de ejemplo también

document.addEventListener('DOMContentLoaded', ()=>{
    const url = 'http://localhost/ApiBiblioteca-MAIN/api/libros';
    
    //realizo la llamada a la api para conseguir los datos
    fetch(url)
    .then (response=>response.json()) //la guardo en la variable response y la transformo en json
    .then(data =>mostrarLibros(data))
    .catch(error => console.error('Error:', error));
})

function mostrarLibros(datos){
// console.log(datos.count)
// console.log(datos.sucess)
// console.log(datos.data)
    const libros = datos.data;
    console.log(libros)
    if(datos.success && datos.count > 0){
        //muestro los libros por pantalla
        document.getElementById('divLibros').innerHTML = 
        libros.map(libro => `
            <div class="libro">
        <img src="img/imgPequenias/${libro.img}" alt="${libro.titulo}">
        <h3>${libro.titulo}</h3>
        <p>${libro.resumen}</p>
        </div>
            `).join('')
    }else if(datos.count == 0){
        document.getElementById('divLibros').innerHTML = "<p>No hay libros</p>";
    }
}