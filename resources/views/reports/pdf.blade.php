<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .meta {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
            color: #555;
        }

        .class-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .late {
            color: #b91c1c;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>

<h2>Laporan Absensi Siswa</h2>

<div class="meta">
    @php
        $kelas = request('class_room_id')
            ? \App\Models\ClassRoom::find(request('class_room_id'))->name ?? '-'
            : 'Semua Kelas';

        $periode = request('date')
            ? request('date')
            : (request('month') ? request('month') : 'Semua Tanggal');
    @endphp

    Kelas: <strong>{{ $kelas }}</strong> |
    Periode: <strong>{{ $periode }}</strong>
</div>

@php
    $grouped = $data->groupBy(fn($a) => $a->students->classRoom->name ?? 'Tanpa Kelas');
@endphp

@foreach($grouped as $className => $rows)

<div class="class-title">Kelas {{ $className }}</div>

<table>
    <thead>
        <tr>
            <th style="width:12%">Tanggal</th>
            <th style="width:28%">Nama</th>
            <th style="width:15%">Jam</th>
            <th style="width:15%">Status</th>
            <th style="width:30%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="center">{{ $row->date }}</td>
            <td>{{ $row->students->name }}</td>
            <td class="center">{{ $row->time_in ?? '-' }}</td>
            <td class="center {{ $row->status === 'TERLAMBAT' ? 'late' : '' }}">
                {{ $row->status }}
            </td>
            <td>
                {{ $row->is_late ? 'Datang melewati jam masuk' : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endforeach

<div class="footer">
    Dicetak pada {{ now()->format('d-m-Y H:i') }}
</div>

</body>
</html>
