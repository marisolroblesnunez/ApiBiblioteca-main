//cuando hicimos la api del tiempo hicimos esto, lo puedo coger de ejemplo también

document.addEventListener('DOMContentLoaded', ()=>{
    const url = 'http://localhost/ApiBiblioteca-MAIN/api/libros';
  const divLibros = document.getElementById('divLibros');
  const modal      = document.getElementById('modal');
  const modalImg   = document.getElementById('modal-imagen');
  const modalInfo  = document.getElementById('modal-info');
  const cerrarBtn  = document.querySelector('.cerrar');

  let librosGlobal = [];

    
    //realizo la llamada a la api para conseguir los datos
    fetch(url)
    .then (response=>response.json()) //la guardo en la variable response y la transformo en json
    .then(data =>mostrarLibros(data))
    .catch(error => console.error('Error:', error));

 function mostrarLibros(datos) {
    librosGlobal = datos.data;   // guardamos globalmente
    if (datos.success && datos.count > 0) {
      divLibros.innerHTML = librosGlobal.map(libro => `
        <div class="libro">
          <img src="img/imgPequenias/${libro.img}" alt="${libro.titulo}">
          <h3>${libro.titulo}</h3>
          <p>${libro.resumen}</p>
        </div>
      `).join('');

      // Ahora enlazamos clic a cada .libro
      document.querySelectorAll('.libro').forEach((el, idx) => {
        el.addEventListener('click', () => openModal(librosGlobal[idx]));
      });

    } else if (datos.count === 0) {
      divLibros.innerHTML = "<p>No hay libros</p>";
    }
  }

  function openModal(libro) {
    console.log(libro)
    modalImg.src = `img/${libro.img}`; 
    modalImg.alt = libro.titulo;
    modalInfo.innerHTML = `
      <strong>Género:</strong> ${libro.genero}<br>
      <strong>Publicado:</strong> ${libro.fecha_publicacion}<br>
      <strong>Disponible:</strong> ${libro.disponible ? 'Sí' : 'No'}
    `;
    modal.classList.remove('hidden');
  }

  cerrarBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Cerrar al hacer clic fuera del contenido
  modal.addEventListener('click', e => {
    if (e.target === modal) modal.classList.add('hidden');
  });
});