
document.getElementById("formRestablecer").addEventListener("submit", function(e) {
    const nueva = document.getElementById("nueva_password").value;
    const confirmar = document.getElementById("confirmar_password").value;
    const mensaje = document.getElementById("mensaje_cliente");

    if (nueva !== confirmar) {
        e.preventDefault(); // Evita que se envíe el formulario
        mensaje.textContent = "Las contraseñas no coinciden.";
        mensaje.style.display = 'block';
   
}
});
