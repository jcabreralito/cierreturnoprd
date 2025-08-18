import './bootstrap';

/**
 * Función para mostrar el loader de cargando
 *
 * @return void
 */
function showLoader() {
    const loader = document.getElementById('loader');
    loader.classList.remove('hidden'); // Muestra el loader
    loader.classList.add('flex'); // Cambia la clase a flex
}

/**
 * Función para ocultar el loader de cargando
 *
 * @return void
 */
function hideLoader() {
    const loader = document.getElementById('loader');
    loader.classList.remove('flex'); // Cambia la clase a flex
    loader.classList.add('hidden'); // Oculta el loader
}
