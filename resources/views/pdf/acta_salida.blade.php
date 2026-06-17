<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Conciliación/Despacho #{{ $salida->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f27b00; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; color: #666; margin: 5px 0 0 0; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 5px; vertical-align: top; }
        .info-label { font-weight: bold; width: 120px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .signatures { width: 100%; margin-top: 50px; text-align: center; }
        .signatures td { width: 50%; padding-top: 50px; }
        .sign-line { border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px; }
    </style>
</head>
<body>

<div class="header">
    <h1 class="title">PLANTA DE ASFALTO - GAMEA</h1>
    <h2 class="subtitle">ACTA DE CONCILIACIÓN / DESPACHO #{{ str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</h2>
</div>

<table class="info-table">
    <tr>
        <td class="info-label">Fecha de Salida:</td>
        <td>{{ $salida->fecha_salida }}</td>
        <td class="info-label">O.D.C.:</td>
        <td>{{ $salida->odc ?? 'S/C' }}</td>
    </tr>
    <tr>
        <td class="info-label">Registrado por:</td>
        <td>{{ $salida->usuario?->name ?? 'Sistema' }}</td>
        <td class="info-label">Funcionario Resp.:</td>
        <td>{{ $salida->funcionario?->nombre ?? 'N/A' }} ({{ $salida->funcionario?->cargo ?? '' }})</td>
    </tr>
    <tr>
        <td class="info-label">Proyecto / Destino:</td>
        <td>{{ $salida->proyecto?->nombre ?? 'N/A' }}</td>
        <td class="info-label">Uso / Motivo:</td>
        <td>{{ $salida->uso ?? 'S/C' }}</td>
    </tr>
    <tr>
        <td class="info-label">Observaciones:</td>
        <td colspan="3">{{ $salida->observaciones ?? 'Ninguna' }}</td>
    </tr>
</table>

<h3>Detalle de Materiales Utilizados</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Nro Lote / RGTR</th>
            <th>Material / Insumo</th>
            <th>Unidad</th>
            <th class="text-right">Cant. Utilizada</th>
            <th>Acción Planificada</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salida->detalles as $detalle)
        <tr>
            <td>{{ $detalle->detalleIngreso?->nro_registro }}</td>
            <td>{{ $detalle->detalleIngreso?->material?->nombre }}</td>
            <td>{{ $detalle->detalleIngreso?->material?->medida?->abreviacion }}</td>
            <td class="text-right">{{ number_format($detalle->cantidad_salida, 2) }}</td>
            <td>{{ $detalle->detalleIngreso?->acciones_planificadas ?? 'S/R' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sign-line">Despachado Por (Almacén)</div>
            <div>{{ $salida->usuario?->name }}</div>
        </td>
        <td>
            <div class="sign-line">Recibido / Conciliado Por (Funcionario)</div>
            <div>{{ $salida->funcionario?->nombre }}</div>
        </td>
    </tr>
</table>

</body>
</html>
