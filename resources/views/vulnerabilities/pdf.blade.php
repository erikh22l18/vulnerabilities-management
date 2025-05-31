<!-- resources/views/vulnerabilities/pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Vulnerabilidad - {{ $vulnerability->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; font-size: 12px; }
        .container { padding: 20px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 20px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #555; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; color: #444; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; } /* No funciona bien en dompdf sin flex o tablas complejas */
        .detail-item { margin-bottom: 8px; }
        .detail-item strong { color: #555; }
        .footer { text-align: center; font-size: 10px; color: #777; position: fixed; bottom: 10px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detalle de Vulnerabilidad</h1>

        <div class="section-title">Información General</div>
        <table>
            <tr>
                <th>ID de Vulnerabilidad:</th>
                <td>#{{ $vulnerability->id }}</td>
            </tr>
            <tr>
                <th>Título:</th>
                <td>{{ $vulnerability->title }}</td>
            </tr>
            <tr>
                <th>Proyecto:</th>
                <td>{{ $vulnerability->project->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Organización:</th>
                <td>{{ $vulnerability->project->organization->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Componente Afectado:</th>
                <td>{{ $vulnerability->component ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Estado Actual:</th>
                <td>{{ $vulnerability->state ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Severidad:</th>
                <td>{{ $vulnerability->severity_level ?? 'N/A' }}</td>
            </tr>
             <tr>
                <th>Prioridad:</th>
                <td>{{ $vulnerability->priority ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Descripción</div>
        <p>{{ $vulnerability->description ?? 'No se proporcionó descripción.' }}</p>

        <div class="section-title">Detalles Técnicos</div>
        <table>
            <tr>
                <th>Tipo de Vulnerabilidad:</th>
                <td>{{ $vulnerability->type->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Categoría de Vulnerabilidad:</th>
                <td>{{ $vulnerability->category->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Clasificación OWASP:</th>
                <td>{{ $vulnerability->owasp_classification ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Puntuación CVSS:</th>
                <td>{{ $vulnerability->cvss_score ?? 'N/A' }} (Vector: {{ $vulnerability->cvss_vector ?? 'N/A' }})</td>
            </tr>
        </table>

        <div class="section-title">Gestión y Fechas</div>
        <table>
            <tr>
                <th>Detectada por:</th>
                <td>{{ $vulnerability->creator->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha de Detección:</th>
                <td>{{ $vulnerability->detection_date ? \Carbon\Carbon::parse($vulnerability->detection_date)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Fecha Límite de Resolución:</th>
                <td>{{ $vulnerability->resolution_deadline ? \Carbon\Carbon::parse($vulnerability->resolution_deadline)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
             <tr>
                <th>Fuente de Detección:</th>
                <td>{{ $vulnerability->detection_source ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Usuarios Asignados</div>
        @if($vulnerability->assignedUsers && $vulnerability->assignedUsers->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vulnerability->assignedUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay usuarios asignados a esta vulnerabilidad.</p>
        @endif
        
        {{-- Considerar añadir comentarios si es relevante para el informe --}}
        {{-- <div class="section-title">Comentarios</div> --}}

    </div>
    <div class="footer">
        Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
