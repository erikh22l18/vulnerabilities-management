<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Informe de Vulnerabilidades - {{ $project->name }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; /* Soporte para UTF-8 */
            font-size: 9px; /* Reducido para más info en A4 landscape */
            margin: 0; 
            padding: 0;
        }
        @page {
            margin: 20mm 15mm; /* Márgenes de la página */
        }
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header {
            top: -15mm; /* Ajustar según el margen superior */
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            bottom: -15mm; /* Ajustar según el margen inferior */
            font-size: 8px;
            color: #777;
        }
        .footer .page-number:after {
            content: counter(page);
        }
        h1 { 
            text-align: center; 
            font-size: 16px; 
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        h2 { 
            font-size: 13px; 
            margin-top: 15px;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 1px dotted #666;
            padding-bottom: 3px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 5px; /* Aumentado ligeramente para mejor lectura */
            text-align: left; 
            word-break: break-word; /* Para evitar que texto largo rompa la tabla */
            vertical-align: top; /* Alineación superior para celdas con mucho texto */
        }
        th { 
            background-color: #e9e9e9; 
            font-weight: bold; /* Asegurar que los encabezados sean negrita */
        }
        .page-break { 
            page-break-after: always; 
        }
        .info-block {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .info-block p {
            margin: 0 0 5px 0;
        }
        .info-block strong {
            display: inline-block;
            width: 150px; /* Ancho fijo para etiquetas */
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 30%; text-align: left; border: none;">
                    <!-- **TODO: Add Organization Logo Here** -->
                    <span style="font-size: 10px; color: #777;">[Logo de Organización Placeholder]</span>
                </td>
                <td style="width: 70%; text-align: right; border: none; font-size: 12px; font-weight: bold;">
                    Informe Consolidado de Vulnerabilidades
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ $project->name }} - Página <span class="page-number"></span> - Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>

    <main>
        <h1>Informe de Vulnerabilidades del Proyecto</h1>
        
        <div class="info-block">
            <p><strong>Nombre del Proyecto:</strong> {{ $project->name }}</p>
            <p><strong>Identificador:</strong> {{ $project->identifier }}</p>
            <p><strong>Organización:</strong> {{ $project->organization->name ?? 'N/A' }}</p>
            <p><strong>Estado del Proyecto:</strong> {{ ucfirst($project->status ?? 'N/A') }}</p>
            <p><strong>Objetivo General:</strong> {{ $project->general_objective ?? 'No especificado' }}</p>
            <p><strong>Fecha del Informe:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>

        <h2>Resumen de Vulnerabilidades</h2>
        <div class="info-block" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee; background-color: #f9f9f9;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 50%; vertical-align: top; border: none; padding-right: 10px;">
                        <p style="margin-bottom: 5px;"><strong>Por Estado:</strong></p>
                        <ul style="list-style-type: disc; margin-left: 20px; padding-left: 0;">
                            @forelse($vulnerabilityStatsByStatus as $status => $count)
                                <li>{{ ucfirst($status) }}: {{ $count }}</li>
                            @empty
                                <li>N/A</li>
                            @endforelse
                        </ul>
                    </td>
                    <td style="width: 50%; vertical-align: top; border: none; padding-left: 10px;">
                        <p style="margin-bottom: 5px;"><strong>Por Severidad:</strong></p>
                        <ul style="list-style-type: disc; margin-left: 20px; padding-left: 0;">
                            @forelse($vulnerabilityStatsBySeverity as $severity => $count)
                                <li>{{ ucfirst($severity) }}: {{ $count }}</li>
                            @empty
                                <li>N/A</li>
                            @endforelse
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <h2>Listado de Vulnerabilidades</h2>
        @if($vulnerabilities && $vulnerabilities->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width:5%;">ID</th>
                        <th style="width:20%;">Título</th>
                        <th style="width:10%;">Estado</th>
                        <th style="width:10%;">Severidad</th>
                        <th style="width:15%;">Tipo</th>
                        <th style="width:15%;">Responsables</th>
                        <th style="width:10%;">Fecha Detección</th>
                        <th style="width:10%;">Fecha Límite</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vulnerabilities as $vuln)
                        <tr>
                            <td>#{{ $vuln->id }}</td>
                            <td>{{ $vuln->title }}</td>
                            <td>{{ $vuln->state }}</td>
                            <td>{{ $vuln->severity_level }}</td>
                            <td>{{ $vuln->type->name ?? 'N/A' }}</td>
                            <td>
                                @if($vuln->assignedUsers->isNotEmpty())
                                    {{ $vuln->assignedUsers->pluck('name')->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $vuln->detection_date ? \Carbon\Carbon::parse($vuln->detection_date)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $vuln->resolution_deadline ? \Carbon\Carbon::parse($vuln->resolution_deadline)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total de vulnerabilidades: {{ $vulnerabilities->count() }}</p>
        @else
            <p>No hay vulnerabilidades registradas para este proyecto.</p>
        @endif
    </main>
</body>
</html>
