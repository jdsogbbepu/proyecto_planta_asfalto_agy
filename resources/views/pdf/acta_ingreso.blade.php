<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Ingreso #{{ $ingreso->id }}</title>
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
    <h2 class="subtitle">ACTA DE INGRESO DE MATERIALES #{{ str_pad($ingreso->id, 6, '0', STR_PAD_LEFT) }}</h2>
</div>

<table class="info-table">
    <tr>
        <td class="info-label">Fecha de Registro:</td>
        <td>{{ $ingreso->fecha_ingreso }}</td>
        <td class="info-label">O.D.C.:</td>
        <td>{{ $ingreso->odc ?? 'S/C' }}</td>
    </tr>
    <tr>
        <td class="info-label">Registrado por:</td>
        <td>{{ $ingreso->usuario?->name ?? 'Sistema' }}</td>
        <td class="info-label">Funcionario Resp.:</td>
        <td>{{ $ingreso->funcionario?->nombre ?? 'N/A' }} ({{ $ingreso->funcionario?->cargo ?? '' }})</td>
    </tr>
    <tr>
        <td class="info-label">Observaciones:</td>
        <td colspan="3">{{ $ingreso->observaciones ?? 'Ninguna' }}</td>
    </tr>
</table>

<h3>Detalle de Materiales Recibidos</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Nro Registro</th>
            <th>Material / Insumo</th>
            <th>Unidad</th>
            <th>Proveedor</th>
            <th>Proyecto / Destino</th>
            <th class="text-right">Cant. Adquirida</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ingreso->detalles as $detalle)
        <tr>
            <td>{{ $detalle->nro_registro }}</td>
            <td>{{ $detalle->material?->nombre }}</td>
            <td>{{ $detalle->material?->medida?->abreviacion }}</td>
            <td>{{ $detalle->proveedor?->razon_social }}</td>
            <td>{{ $detalle->proyecto?->nombre }}</td>
            <td class="text-right">{{ number_format($detalle->cantidad_adquirida, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sign-line">Entregado Por (Proveedor / Chofer)</div>
        </td>
        <td>
            <div class="sign-line">Recibido Conforme (Funcionario)</div>
            <div>{{ $ingreso->funcionario?->nombre }}</div>
        </td>
    </tr>
</table>

</body>
</html>
