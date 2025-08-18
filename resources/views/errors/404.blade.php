<x-error-layout>
    <div class="mx-auto grid place-content-center h-full absolute w-full">
        {{--  Personalizacion de vista de error 404  --}}
        <div>
            <div class="w-6/12 mx-auto">
                <img src="{{ asset('assets/img/404.svg') }}" alt="404">
            </div>

            <div class="mt-16 space-y-6">
                <h1 class="text-4xl font-bold text-center text-sky-500">¡Ups! Página no encontrada</h1>
                <p class="text-center text-blue-gray-500">La página que buscas no existe o ha sido
                    eliminada.</p>

            <div class="flex justify-center mt-6">
                <a href="{{ route('index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    <i class="fas fa-arrow-left"></i>
                    <span>Regresar a inicio</span>
                </a>
            </div>
        </div>
    </div>
</x-main-layout>
