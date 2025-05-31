<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $viewModel->title }}</h1>
                </div>

                <form action="{{ route('vulnerabilities.tasks.store', $viewModel->vulnerability) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-label for="title" value="Título" />
                            <x-input id="title" name="title" type="text" class="mt-1 block w-full" 
                                required autofocus />
                            <x-input-error for="title" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="description" value="Descripción" />
                            <x-textarea id="description" name="description" 
                                class="mt-1 block w-full" rows="3" />
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="assigned_to" value="Asignar a" />
                            <x-select id="assigned_to" name="assigned_to" class="mt-1 block w-full">
                                <option value="">Seleccionar usuario...</option>
                                @foreach($viewModel->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="assigned_to" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="priority" value="Prioridad" />
                            <x-select id="priority" name="priority" class="mt-1 block w-full" required>
                                @foreach($viewModel->priorities as $priority)
                                    <option value="{{ $priority }}">{{ ucfirst($priority) }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="priority" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="due_date" value="Fecha límite" />
                            <x-input id="due_date" name="due_date" type="date" 
                                class="mt-1 block w-full" />
                            <x-input-error for="due_date" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        @if($viewModel->backRoute)
                            <a href="{{ $viewModel->backRoute }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                                ← Volver
                            </a>
                        @endif

                        <x-button>
                            Crear Tarea
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>