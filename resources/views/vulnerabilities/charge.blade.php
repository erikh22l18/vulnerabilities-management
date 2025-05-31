<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Importar Vulnerabilidades desde Excel</h1>
                    <a href="{{ route('vulnerabilities.template') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        Descargar plantilla
                    </a>
                </div>
                @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
                @endif
                @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 px-4 py-2 rounded">
                    {{ $errors->first() }}
                </div>
                @endif
                <form action="{{ route('vulnerabilities.upload') }}" method="POST" enctype="multipart/form-data" id="excel-upload-form">
                    @csrf
                    <div
                        id="drop-area"
                        class="flex flex-col items-center justify-center border-2 border-dashed border-blue-400 rounded-lg p-8 bg-blue-50 hover:bg-blue-100 transition cursor-pointer"
                        ondrop="handleDrop(event)"
                        ondragover="handleDragOver(event)"
                        onclick="document.getElementById('excel-file').click();">
                        <svg class="w-12 h-12 text-blue-400 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16v-4a4 4 0 014-4h2a4 4 0 014 4v4m-4 4v-4m0 0l-4-4m4 4l4-4" />
                        </svg>
                        <span class="text-blue-700 font-semibold">Arrastra y suelta tu archivo Excel aquí</span>
                        <span class="text-gray-500 text-sm mt-1">o haz clic para seleccionar un archivo</span>
                        <input type="file" name="file" id="excel-file" accept=".xlsx,.xls" class="hidden" required>
                    </div>
                    <div id="file-name" class="mt-2 text-gray-700"></div>
                    <div class="flex items-center space-x-4 mt-6" id="validation-icons">
                        <span id="icon-header" class="text-gray-400" title="Validar encabezado">
                            <!-- Check icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                <path d="M9 12l2 2l4 -4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span id="icon-form" class="text-gray-400" title="Validar datos del formulario">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                <path d="M9 12l2 2l4 -4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span id="icon-upload" class="text-gray-400" title="Carga exitosa">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                <path d="M9 12l2 2l4 -4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('vulnerabilities.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition" id="import-btn">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('excel-file');
        const fileName = document.getElementById('file-name');
        const form = document.getElementById('excel-upload-form');
        const importBtn = document.getElementById('import-btn');
        const icons = {
            header: document.getElementById('icon-header'),
            form: document.getElementById('icon-form'),
            upload: document.getElementById('icon-upload')
        };

        function setIconStatus(icon, status) {
            // status: 'gray', 'green', 'red'
            icon.classList.remove('text-gray-400', 'text-green-500', 'text-red-500');
            if (status === 'green') icon.classList.add('text-green-500');
            else if (status === 'red') icon.classList.add('text-red-500');
            else icon.classList.add('text-gray-400');
        }

        function resetIcons() {
            setIconStatus(icons.header, 'gray');
            setIconStatus(icons.form, 'gray');
            setIconStatus(icons.upload, 'gray');
        }

        function handleDragOver(e) {
            e.preventDefault();
            dropArea.classList.add('bg-blue-100');
        }

        function handleDrop(e) {
            e.preventDefault();
            dropArea.classList.remove('bg-blue-100');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = e.dataTransfer.files[0].name;
            }
        }

        fileInput.addEventListener('change', function() {
            if (fileInput.files.length) {
                fileName.textContent = fileInput.files[0].name;
                resetIcons();
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            importBtn.disabled = true;
            resetIcons();

            const file = fileInput.files[0];
            if (!file) {
                setIconStatus(icons.header, 'red');
                importBtn.disabled = false;
                alert('Selecciona un archivo.');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            // Paso 1: Validar encabezado y proyectos
            fetch('{{ route('vulnerabilities.validateHeader') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        setIconStatus(icons.header, 'red');
                        importBtn.disabled = false;
                        alert(data.message);
                        return;
                    }

                    setIconStatus(icons.header, 'green');

                    // Paso 2: Validar contenido del formulario (campos por fila)
                    setIconStatus(icons.form, 'gray');
                    return fetch('{{ route('vulnerabilities.validateRows') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                })
                .then(res => res ? res.json() : null)
                .then(data => {
                    if (data && !data.success) {
                        setIconStatus(icons.form, 'red');
                        importBtn.disabled = false;
                        alert(data.message);
                        return;
                    }

                    if (data) {
                        setIconStatus(icons.form, 'green');
                    }

                    // Paso 3: Subida real
                    setIconStatus(icons.upload, 'green');
                    form.submit();
                })
                .catch(() => {
                    setIconStatus(icons.header, 'red');
                    importBtn.disabled = false;
                    alert('Error validando el archivo.');
                });
        });
    </script>
</x-app-layout>