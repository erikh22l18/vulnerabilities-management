<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Errores de Importación: {{ Str::limit($importBatch->original_filename, 50) }}
            </h2>
            <a href="{{ route('vulnerabilities.imports.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                &laquo; Volver al Historial de Importaciones
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

                    <div class="mb-6 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>ID del Lote:</strong> {{ $importBatch->id }}</p>
                        <p><strong>Usuario:</strong> {{ $importBatch->user->name ?? 'N/A' }}</p>
                        <p><strong>Estado del Lote:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($importBatch->status === 'completed_successfully') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @elseif($importBatch->status === 'completed_with_errors') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                @elseif($importBatch->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ Str::title(str_replace('_', ' ', $importBatch->status)) }}
                            </span>
                        </p>
                        @if($importBatch->error_summary)
                        <p><strong>Resumen del Error del Lote:</strong> <span class="text-red-600 dark:text-red-400">{{ $importBatch->error_summary }}</span></p>
                        @endif
                        <p><strong>Total de Filas Intentadas:</strong> {{ $importBatch->total_rows ?? ($importBatch->successful_rows + $importBatch->failed_rows) }}</p>
                        <p><strong>Filas Exitosas:</strong> <span class="text-green-600 dark:text-green-400">{{ $importBatch->successful_rows }}</span></p>
                        <p><strong>Filas Fallidas:</strong> <span class="text-red-600 dark:text-red-400">{{ $importBatch->failed_rows }}</span></p>
                        <p><strong>Fecha de Procesamiento:</strong> {{ $importBatch->completed_at ? $importBatch->completed_at->format('Y-m-d H:i:s') : ($importBatch->started_at ? $importBatch->started_at->format('Y-m-d H:i:s') . ' (en proceso)' : 'N/A') }}</p>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4">Detalles de Errores por Fila:</h3>
                    @if($rowErrors->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"># Fila Archivo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Errores</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datos Originales (JSON)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($rowErrors as $error)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $error->row_number }}</td>
                                            <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400">
                                                @if(is_array($error->error_messages))
                                                    <ul class="list-disc pl-5">
                                                    @foreach($error->error_messages as $msgKey => $msgContent)
                                                        @if(is_array($msgContent))
                                                            <li><strong>{{ Str::title(str_replace('_', ' ', $msgKey)) }}:</strong> {{ implode(', ', $msgContent) }}</li>
                                                        @else
                                                            <li>{{ $msgContent }}</li>
                                                        @endif
                                                    @endforeach
                                                    </ul>
                                                @else
                                                    {{ $error->error_messages }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                <pre class="whitespace-pre-wrap text-xs bg-gray-100 dark:bg-gray-900 p-2 rounded">{{ json_encode($error->row_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $rowErrors->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No se encontraron errores detallados para las filas de este lote, o el job falló antes de poder procesar y registrar errores de filas individuales.</p>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('vulnerabilities.imports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-500 focus:ring focus:ring-gray-200 dark:focus:ring-gray-600 disabled:opacity-25 transition">
                            &laquo; Volver al Historial de Importaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
