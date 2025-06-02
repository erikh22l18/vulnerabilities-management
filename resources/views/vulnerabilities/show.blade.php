<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Detalle de Vulnerabilidad</h1>
                    <div class="flex gap-2">
                        <a href="{{ route('vulnerabilities.pdf', $vulnerability) }}"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar PDF
                        </a>
                    </div>
                </div>
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ $vulnerability->title }}</h1>
                <div class="bg-blue-50 p-6 rounded-lg shadow mb-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    
                        <p class="mb-2"><span class="font-semibold text-gray-700">Estado:</span>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                {{ $vulnerability->state === 'Detectada' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $vulnerability->state === 'En análisis' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $vulnerability->state === 'En tratamiento' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $vulnerability->state === 'Resuelta' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $vulnerability->state === 'Cerrada' ? 'bg-gray-200 text-gray-600' : '' }}">
                                {{ $vulnerability->state ?? 'N/A' }}
                            </span>
                        </p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Proyecto:</span> {{ $vulnerability->project->name ?? '-' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Componente:</span> {{ $vulnerability->component ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Tipo:</span> {{ $vulnerability->type->name ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Clasificación OWASP:</span> {{ $vulnerability->owasp_classification ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Vector CVSS:</span> {{ $vulnerability->cvss_vector ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Puntaje CVSS:</span> {{ $vulnerability->cvss_score ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Nivel de severidad:</span> {{ $vulnerability->severity_level ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Probabilidad de explotación:</span> {{ $vulnerability->exploit_probability ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Impacto estimado:</span> {{ $vulnerability->estimated_impact ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Prioridad:</span> {{ $vulnerability->priority ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Fuente de detección:</span> {{ $vulnerability->detection_source ?? 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Fecha de detección:</span> {{ $vulnerability->detection_date ? \Carbon\Carbon::parse($vulnerability->detection_date)->format('d/m/Y') : 'N/A' }}</p>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Fecha límite:</span> {{ $vulnerability->resolution_deadline ? \Carbon\Carbon::parse($vulnerability->resolution_deadline)->format('d/m/Y') : 'N/A' }}</p>
                        <p class="mb-2 col-span-2"><span class="font-semibold text-gray-700">Descripción:</span><br>
                            <span class="text-gray-700">{{ $vulnerability->description ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-2 col-span-2"><span class="font-semibold text-gray-700">Observaciones:</span><br>
                            <span class="text-gray-700">{{ $vulnerability->observations ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-2 col-span-2"><span class="font-semibold text-gray-700">Enlace a documentación:</span>
                            @if($vulnerability->documentation_url)
                            <a href="{{ $vulnerability->documentation_url }}" target="_blank" class="text-blue-600 underline break-all">{{ $vulnerability->documentation_url }}</a>
                            @else
                            <span class="text-gray-500">-</span>
                            @endif
                        </p>
                </div>

                {{-- Sección para Cambiar Estado --}}
                @if ($vulnerability->state !== App\Domain\Vulnerabilities\Models\Vulnerability::STATE_CERRADA || 
                     (isset(App\Domain\Vulnerabilities\Models\Vulnerability::$stateTransitions[App\Domain\Vulnerabilities\Models\Vulnerability::STATE_CERRADA]) && 
                      count(App\Domain\Vulnerabilities\Models\Vulnerability::$stateTransitions[App\Domain\Vulnerabilities\Models\Vulnerability::STATE_CERRADA]) > 0)
                    )
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Estado de la Vulnerabilidad</h3>
                        <form action="{{ route('vulnerabilities.change-state', $vulnerability) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="new_state" class="block font-medium text-gray-700 mb-1">Nuevo Estado</label>
                                <select name="new_state" id="new_state" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                                    <option value="">Seleccione un nuevo estado...</option>
                                    @php
                                        $currentState = $vulnerability->state;
                                        $allowedTransitions = App\Domain\Vulnerabilities\Models\Vulnerability::$stateTransitions[$currentState] ?? [];
                                    @endphp
                                    @foreach ($allowedTransitions as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                                @error('new_state') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="justification" class="block font-medium text-gray-700 mb-1">Justificación del Cambio de Estado</label>
                                <textarea name="justification" id="justification" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>{{ old('justification') }}</textarea>
                                @error('justification') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow transition">Actualizar Estado</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mt-6 pt-6 border-t border-gray-200">
                         <p class="text-gray-600">La vulnerabilidad está cerrada y no se puede cambiar su estado (a menos que se defina una reapertura específica).</p>
                    </div>
                @endif


                {{-- Sección para Añadir Comentarios (simplificada) --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Añadir Comentario</h3>
                    <form action="{{ route('vulnerabilities.comment', $vulnerability) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="comment_text" class="block font-medium text-gray-700 mb-1">Comentario</label>
                            <textarea name="comment" id="comment_text" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>{{ old('comment') }}</textarea>
                            @error('comment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded shadow transition">Añadir Comentario</button>
                        </div>
                    </form>
                </div>

                {{-- Historial de Comentarios y Cambios de Estado --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Comentarios y Cambios de Estado</h3>
                    @if($vulnerability->comments && $vulnerability->comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($vulnerability->comments->sortByDesc('created_at') as $comment)
                                <div class="p-4 border rounded {{ $comment->state_before ? 'bg-yellow-50' : 'bg-gray-50' }}">
                                    <p class="text-sm text-gray-800">{{ $comment->comment }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Por: {{ $comment->user->name ?? 'N/A' }} - {{ $comment->created_at->format('d/m/Y H:i') }}
                                        @if($comment->state_before && $comment->state_after)
                                            <span class="font-semibold">(Estado cambiado de '{{ $comment->state_before }}' a '{{ $comment->state_after }}')</span>
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No hay comentarios ni historial de cambios de estado.</p>
                    @endif
                </div>


                <div class="bg-blue-50 p-6 rounded-lg shadow mb-8 mt-8"> {{-- Añadido mt-8 para separar visualmente --}}
                    <div class="mt-4">
                        <h3 class="font-semibold text-gray-700 mb-2">Usuarios Asignados</h3>
                        @if($vulnerability->assignedUsers && $vulnerability->assignedUsers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                            @foreach($vulnerability->assignedUsers as $user)
                            <div class="flex items-center justify-between bg-white p-2 rounded shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-gray-700">{{ $user->name }}</span>
                                </div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full
                                                {{ $user->pivot->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                                {{ $user->pivot->role === 'analyst' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $user->pivot->role === 'viewer' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($user->pivot->role) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500">No hay usuarios asignados</p>
                        @endif
                    </div>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow mb-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="mb-2"><span class="font-semibold text-gray-700">Archivos adjuntos:</span>
                            @if(isset($vulnerability->attachments) && is_array($vulnerability->attachments) && count($vulnerability->attachments))
                        <ul class="list-disc ml-5">
                            @foreach($vulnerability->attachments as $file)
                            <li>
                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-600 underline">{{ basename($file) }}</a>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <span class="text-gray-500">-</span>
                        @endif
                        </p>
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <a href="{{ route('vulnerabilities.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                        ← Volver al listado
                    </a>
                    <div class="flex gap-2">
                        <a href="{{ route('vulnerabilities.users.index', $vulnerability) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Gestionar Usuarios
                        </a>
                        <a href="{{ route('vulnerabilities.edit', $vulnerability) }}"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded shadow transition">
                            Editar
                        </a>
                        @can('create', [App\Domain\Tasks\Models\Task::class, $vulnerability])
                            <a href="{{ route('vulnerabilities.tasks.create', $vulnerability) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Añadir Tarea
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>