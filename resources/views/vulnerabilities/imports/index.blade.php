<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Historial de Importación de Vulnerabilidades
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Archivo Original</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progreso</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filas Totales</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Exitosas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fallidas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha de Carga</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($importBatches as $batch)
                                    <tr id="batch-row-{{ $batch->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ Str::limit($batch->original_filename, 40) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $batch->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span id="batch-status-{{ $batch->id }}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($batch->status === 'completed_successfully') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($batch->status === 'completed_with_errors') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($batch->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @elseif($batch->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                {{ Str::title(str_replace('_', ' ', $batch->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" id="batch-progress-cell-{{ $batch->id }}">
                                            <div id="batch-progress-bar-container-{{ $batch->id }}" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700" style="display: {{ $batch->status === 'processing' || $batch->status === 'pending' ? 'block' : 'none' }};">
                                                <div id="batch-progress-bar-{{ $batch->id }}" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-linear" style="width: {{ ($batch->total_rows > 0 && ($batch->status === 'processing' || $batch->status === 'pending')) ? (($batch->successful_rows + $batch->failed_rows) / $batch->total_rows) * 100 : 0 }}%"></div>
                                            </div>
                                            <span id="batch-progress-text-{{ $batch->id }}" class="text-xs">
                                                @if($batch->status === 'processing' || $batch->status === 'pending')
                                                    {{ $batch->successful_rows + $batch->failed_rows }} / {{ $batch->total_rows ?? '?' }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" id="batch-total-rows-{{ $batch->id }}">{{ $batch->total_rows ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" id="batch-successful-rows-{{ $batch->id }}">{{ $batch->successful_rows ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" id="batch-failed-rows-{{ $batch->id }}">{{ $batch->failed_rows ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $batch->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($batch->failed_rows > 0 || $batch->status === 'completed_with_errors' || $batch->status === 'failed')
                                                <a href="{{ route('vulnerabilities.imports.errors', $batch) }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                    Ver Errores
                                                </a>
                                            @elseif($batch->status === 'completed_successfully' && ($batch->failed_rows === 0 || is_null($batch->failed_rows)))
                                                <span class="text-green-600 dark:text-green-400">Sin errores</span>
                                            @elseif($batch->status === 'processing' || $batch->status === 'pending')
                                                <span class="text-blue-600 dark:text-blue-400">Procesando...</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr id="batch-errors-display-row-{{ $batch->id }}" style="display: none;" class="bg-red-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                        <td colspan="9" class="px-6 py-3">
                                            <div class="text-sm text-red-700 dark:text-red-300">
                                                <strong class="block mb-1">Errores de Fila:</strong>
                                                <ul id="batch-errors-list-{{ $batch->id }}" class="list-disc pl-5 space-y-1 text-xs">
                                                    <!-- JS will populate this -->
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">
                                            No hay lotes de importación para mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $importBatches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const batches = {{ Js::from($importBatches->items()) }};

    function updateStatusSpan(batchId, statusText, statusType) {
        const span = document.getElementById(`batch-status-${batchId}`);
        if (!span) return;

        span.textContent = statusText.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        let classes = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ';
        if (statusType === 'success' || statusText === 'completed_successfully') {
            classes += 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        } else if (statusType === 'warning' || statusText === 'completed_with_errors') {
            classes += 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        } else if (statusType === 'error' || statusText === 'failed') {
            classes += 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        } else if (statusType === 'processing' || statusText === 'processing') {
            classes += 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        } else {
            classes += 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        }
        span.className = classes;
    }

    batches.forEach(batch => {
        const batchId = batch.id;
        const initialStatus = batch.status;
        let totalRowsForBatch = parseInt(batch.total_rows || 0);

        const batchStatusSpan = document.getElementById(`batch-status-${batchId}`);
        const progressBarContainer = document.getElementById(`batch-progress-bar-container-${batchId}`);
        const progressBar = document.getElementById(`batch-progress-bar-${batchId}`);
        const progressText = document.getElementById(`batch-progress-text-${batchId}`);
        const errorsDisplayRow = document.getElementById(`batch-errors-display-row-${batchId}`);
        const errorsList = document.getElementById(`batch-errors-list-${batchId}`);

        const totalRowsCell = document.getElementById(`batch-total-rows-${batchId}`);
        const successfulRowsCell = document.getElementById(`batch-successful-rows-${batchId}`);
        const failedRowsCell = document.getElementById(`batch-failed-rows-${batchId}`);

        if (initialStatus === 'processing' || initialStatus === 'pending') {
            if (progressBarContainer) progressBarContainer.style.display = 'block';
            if (progressText && totalRowsForBatch > 0) {
                 const currentProgress = parseInt(batch.successful_rows || 0) + parseInt(batch.failed_rows || 0);
                 progressText.textContent = `${currentProgress} / ${totalRowsForBatch} filas`;
                 if(progressBar && totalRowsForBatch > 0) {
                    progressBar.style.width = `${(currentProgress / totalRowsForBatch) * 100}%`;
                 }
            } else if (progressText) {
                progressText.textContent = 'Iniciando...';
            }

            window.Echo.private('import-batch.' + batchId)
                .listen('.import.started', (e) => {
                    console.log('Import Started:', e);
                    totalRowsForBatch = e.totalRows;
                    if (progressBarContainer) progressBarContainer.style.display = 'block';
                    if (progressBar) progressBar.style.width = '0%';
                    if (progressText) progressText.textContent = `0 / ${totalRowsForBatch} filas procesadas`;
                    if (batchStatusSpan) updateStatusSpan(batchId, 'Processing', 'processing');
                    if (totalRowsCell) totalRowsCell.textContent = totalRowsForBatch;
                    if (successfulRowsCell) successfulRowsCell.textContent = '0';
                    if (failedRowsCell) failedRowsCell.textContent = '0';
                    if (errorsList) errorsList.innerHTML = ''; // Clear previous errors
                    if (errorsDisplayRow) errorsDisplayRow.style.display = 'none';
                })
                .listen('.import.progress', (e) => {
                    console.log('Import Progress:', e);
                    if (!totalRowsForBatch && e.processedRows > 0) { // If totalRows wasn't set by 'started' for some reason
                        // This is a fallback, ideally 'started' event sets totalRowsForBatch
                        // Or we might need to fetch it if this event arrives first.
                        // For now, we'll just display processed if total is unknown.
                         if (progressText) progressText.textContent = `${e.processedRows} filas procesadas`;
                    } else if (totalRowsForBatch > 0) {
                        const percentage = (e.processedRows / totalRowsForBatch) * 100;
                        if (progressBar) progressBar.style.width = percentage + '%';
                        if (progressText) progressText.textContent = `${e.processedRows} / ${totalRowsForBatch} filas procesadas`;
                    }
                })
                .listen('.import.rowFailed', (e) => {
                    console.log('Import Row Failed:', e);
                    if (errorsDisplayRow) errorsDisplayRow.style.display = 'table-row';
                    if (errorsList) {
                        const errorItem = document.createElement('li');
                        errorItem.textContent = `Fila ${e.rowNumber}: ${e.errors.join(', ')}`;
                        errorsList.appendChild(errorItem);
                    }
                    if (batchStatusSpan) updateStatusSpan(batchId, 'Processing with errors', 'warning');
                })
                .listen('.import.completed', (e) => {
                    console.log('Import Completed:', e);
                    updateStatusSpan(batchId, e.status, e.status); // Map status string to type for styling

                    if (progressBar) {
                        progressBar.style.width = '100%';
                        if (e.status === 'completed_successfully') {
                            progressBar.classList.remove('bg-blue-600', 'bg-yellow-500');
                            progressBar.classList.add('bg-green-500');
                        } else if (e.status === 'completed_with_errors') {
                            progressBar.classList.remove('bg-blue-600', 'bg-green-500');
                            progressBar.classList.add('bg-yellow-500');
                        } else { // failed
                            progressBar.classList.remove('bg-blue-600', 'bg-green-500', 'bg-yellow-500');
                            progressBar.classList.add('bg-red-500');
                        }
                    }
                    if (progressText) progressText.textContent = e.message;

                    if (totalRowsCell) totalRowsCell.textContent = totalRowsForBatch > 0 ? totalRowsForBatch : (e.importedCount + e.failedCount);
                    if (successfulRowsCell) successfulRowsCell.textContent = e.importedCount;
                    if (failedRowsCell) failedRowsCell.textContent = e.failedCount;

                    // Optionally hide progress bar after completion, or leave it full.
                    // setTimeout(() => {
                    //    if (progressBarContainer) progressBarContainer.style.display = 'none';
                    // }, 5000);

                    window.Echo.leaveChannel('import-batch.' + batchId);
                });
        }
    });
});
</script>
@endpush
</x-app-layout>
