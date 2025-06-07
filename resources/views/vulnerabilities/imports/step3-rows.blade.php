<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Importar Vulnerabilidades - Paso 3: Validar Datos e Importar</h1>

                {{-- Session Errors (e.g., from previous step's redirect if validation failed there) --}}
                @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 px-4 py-3 rounded-lg shadow border border-red-200" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif
                @if(session('validation_errors'))
                <div class="mb-4 text-red-700 bg-red-100 px-4 py-3 rounded-lg shadow border border-red-200" role="alert">
                    <strong class="font-bold">Errores de validación:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach(session('validation_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- AJAX Validation Errors & Messages Area --}}
                <div id="row-validation-messages-container" class="mb-4">
                    {{-- Errors/Success messages from AJAX call will be injected here --}}
                </div>

                {{-- Row Validation Section --}}
                <div class="mb-6 text-center">
                    <button id="validate-rows-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md shadow-sm transition font-medium text-lg">
                        Validar Datos del Archivo
                    </button>
                    <div id="loading-indicator" class="hidden mt-4 flex justify-center items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700">Validando datos...</span>
                    </div>
                </div>

                <hr class="my-8">

                {{-- Import Form Section --}}
                <form id="import-form" action="{{ route('vulnerabilities.import.step3.submit', ['tempFileId' => $tempFileId]) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('vulnerabilities.import.step2.show', ['tempFileId' => $tempFileId]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md shadow-sm transition text-sm font-medium">
                            ← Volver al Paso 2 (Encabezados)
                        </a>
                        <button type="submit" id="import-submit-btn" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-md shadow-sm transition font-medium opacity-50 cursor-not-allowed" disabled>
                            Confirmar e Importar Vulnerabilidades
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const validateRowsBtn = document.getElementById('validate-rows-btn');
        const importSubmitBtn = document.getElementById('import-submit-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const messagesContainer = document.getElementById('row-validation-messages-container');
        const tempFileId = "{{ $tempFileId }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        validateRowsBtn.addEventListener('click', function() {
            validateRowsBtn.disabled = true;
            importSubmitBtn.disabled = true;
            importSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            loadingIndicator.classList.remove('hidden');
            messagesContainer.innerHTML = ''; // Clear previous messages

            fetch(`{{ route('vulnerabilities.import.step3.validate_rows', ['tempFileId' => $tempFileId]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json' // Though no body is sent, good practice
                },
                // body: JSON.stringify({}) // No body needed for this validation call
            })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                validateRowsBtn.disabled = false;

                if (data.success) {
                    messagesContainer.innerHTML = `<div class="text-green-700 bg-green-100 px-4 py-3 rounded-lg shadow border border-green-200" role="alert">
                                                    <strong class="font-bold">Validación Exitosa:</strong> ${data.message}
                                                 </div>`;
                    importSubmitBtn.disabled = false;
                    importSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    let errorHtml = `<div class="text-red-700 bg-red-100 px-4 py-3 rounded-lg shadow border border-red-200" role="alert">
                                        <strong class="font-bold">Error de Validación:</strong> ${data.message || 'Ocurrieron errores.'}`;
                    if (data.errors && data.errors.length > 0) {
                        errorHtml += '<ul class="list-disc list-inside mt-2">';
                        data.errors.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                        errorHtml += '</ul>';
                    }
                    errorHtml += '</div>';
                    messagesContainer.innerHTML = errorHtml;
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('hidden');
                validateRowsBtn.disabled = false;
                console.error('Error en la validación AJAX:', error);
                messagesContainer.innerHTML = `<div class="text-red-700 bg-red-100 px-4 py-3 rounded-lg shadow border border-red-200" role="alert">
                                                <strong class="font-bold">Error:</strong> Ocurrió un problema al contactar el servidor para la validación. Por favor, intente de nuevo.
                                             </div>`;
            });
        });
    });
</script>
</x-app-layout>
