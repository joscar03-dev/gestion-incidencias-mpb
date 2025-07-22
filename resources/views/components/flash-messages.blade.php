@if(session('message'))
    <div id="flash-message" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50 shadow-lg">
        <span class="block sm:inline">{{ session('message') }}</span>
        <button onclick="document.getElementById('flash-message').remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </button>
    </div>
    <script>
        // Auto eliminar después de 5 segundos
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) flashMessage.remove();
        }, 5000);
    </script>
@endif

@if(session('error'))
    <div id="flash-error" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50 shadow-lg">
        <span class="block sm:inline">{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </button>
    </div>
    <script>
        // Auto eliminar después de 5 segundos
        setTimeout(() => {
            const flashError = document.getElementById('flash-error');
            if (flashError) flashError.remove();
        }, 5000);
    </script>
@endif
