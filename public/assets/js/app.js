//  Iniciamos a sweetalert para mostrar mensajes de alerta cuando se emite un evento de laravel
window.addEventListener('alert', event => {
    Swal.fire({
        icon: event.detail.type,
        title: event.detail.title,
        text: event.detail.message
    });
})

// Para mostrar un mensaje de tipo toast
window.addEventListener('toast', event => {
    Swal.fire({
        icon: event.detail.type,
        title: event.detail.title,
        text: event.detail.message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
});

// Cuando cargue la pagina actualizamos el valor del atributo data-update-uri="/livewire/update" a la url de la pagina actual
document.addEventListener('DOMContentLoaded', function () {
    // Validamos que solo se ingresen números en los elementos con la clase isNumberInt
    let inputs = document.querySelectorAll('.isNumberInt');

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    // Validamos que solo se ingresen números en los elementos con la clase isNumberFloat
    let inputsFloat = document.querySelectorAll('.isNumberFloat');

    inputsFloat.forEach(input => {
        // Pero que pueda ingresar cantidad con decimales montos
        const regex = /^\d{0,9}(\.\d{1,2})?$/;

        input.addEventListener('input', () => {
            if (!regex.test(input.value)) {
                input.value = input.value.slice(0, -1);

                // Manteemos solo dos decimales
                input.value = parseFloat(input.value).toFixed(2);
            }
        });
    });

    let livewireElements = document.querySelectorAll('[data-update-uri]');
    livewireElements.forEach(element => {
        element.setAttribute('data-update-uri', '/cierreturno/livewire/update');
    });
});

// Validamos si cualquiera de los input de radio se marca, siempre verificamos si esta conectado a red
document.addEventListener('DOMContentLoaded', function () {
    let radios = document.getElementsByClassName('input_eval');

    for (let i = 0; i < radios.length; i++) {
        radios[i].addEventListener('click', function () {
            // verificamos si el dispositivo esta conectado a internet
            if (!navigator.onLine) {
                // Preguntamos para verificar si ya se verifico la conexion a internet
                function checkConnection() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No hay conexión a internet',
                        text: 'Por favor verifique su conexión a internet',
                        confirmButtonText: 'Volver a verificar'
                    }).then(() => {
                        // Si ya verifico la conexion a internet, y esta conectado, entonces se envia el formulario
                        if (navigator.onLine) {
                            radios[i].click();
                        } else {
                            // Si no hay conexión, vuelve a mostrar la alerta
                            checkConnection();
                        }
                    });
                }

                // Llamar a la función para mostrar la alerta y verificar la conexión
                checkConnection();


                return false;
            }
        });
    }
});
