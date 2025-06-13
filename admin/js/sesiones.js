document.getElementById('cerrarSesion').addEventListener('click', () =>{   ////que cuando haaga click en el booton de cerrarsesion me haga:
    const confirmado = confirm("¿Seguro que quieres cerrar sesion?"); /////confirm es un alert que te deja poner si o no
    if(confirmado){
        fetch('logout.php', {
            method: 'POST'
        })
        .then(() => {
            window.location.href = 'login.php'
        })
    }
})