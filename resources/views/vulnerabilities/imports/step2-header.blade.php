<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Importar Vulnerabilidades - Paso 2: Validar Encabezados</h1>

                @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 px-4 py-3 rounded-lg shadow border border-red-200" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif

                <div class="mb-6">
                    <h2 class="text-xl font-medium text-gray-700 mb-2">Encabezados Encontrados en el Archivo:</h2>
                    @if(isset($headers) && count($headers) > 0)
                        <ul class="list-disc list-inside bg-gray-50 p-4 rounded-md shadow border">
                            @foreach($headers as $header)
                                <li class="text-gray-600">{{ $header }}</li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-sm text-gray-600">
                            Por favor, revise los encabezados detectados en su archivo. Si son correctos, continúe al siguiente paso.
                            Si los encabezados no son los esperados, por favor, corrija su archivo y vuelva a cargarlo.
                        </p>
                    @else
                        <p class="text-gray-600 bg-yellow-50 p-4 rounded-md shadow border border-yellow-200">No se encontraron encabezados o el archivo está vacío.</p>
                    @endif
                </div>

                <form action="{{ route('vulnerabilities.import.step2.submit', ['tempFileId' => $tempFileId]) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between mt-8">
                        <a href="{{ route('vulnerabilities.charge') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md shadow-sm transition text-sm font-medium">
                            ← Cancelar y Volver al Paso 1
                        </a>
                        @if(isset($headers) && count($headers) > 0)
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-md shadow-sm transition font-medium">
                            Confirmar Encabezados y Continuar al Paso 3 →
                        </button>
                        @else
                        <button type="submit" class="bg-blue-400 text-white px-6 py-2 rounded-md shadow-sm transition font-medium" disabled>
                            Confirmar Encabezados y Continuar al Paso 3 →
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
