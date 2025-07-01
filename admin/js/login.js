//crear referencias a las modales
const modalRegistro = document.getElementById('modalRegistro');
const modalRecuperar = document.getElementById('modalRecuperar');

//referencias a los enlaces que abren las modales
const btnRecuperar = document.querySelector('.abrir-modal-recuperar');//queryselector te devuelve el primer elemento con esta clase
const btnRegistro = document.querySelector('.abrir-modal-registro');

//referencias al span que cierra la modal
const spanRegistro = document.querySelector('.cerrarRegistro');
const spanRecuperar = document.querySelector('.cerrarRecuperar');

//abrir la modal registro
btnRegistro.addEventListener('click', () => {
    modalRegistro.style.display = 'flex';
})

//cerrar la modal registro
spanRegistro.onclick = function(){
    modalRegistro.style.display = 'none';
}

btnRecuperar.addEventListener('click', () => {
    modalRecuperar.style.display = 'flex';

})

spanRecuperar.addEventListener('click', () => {
    modalRecuperar.style.display = 'none';
})

//cerrar modal cuando el usuario hace click fuera de la modal
window.onclick = function(event){
    if(event.target == modalRegistro){
        modalRegistro.style.display = 'none';
    }
}