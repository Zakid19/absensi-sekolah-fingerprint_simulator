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
            margin-bottom: 4px;
        }

        .meta {
            text-align: center;
            font-size: 10px;
            margin-bottom: 12px;
            color: #555;
        }

        .class-title {
            font-weight: bold;
            margin-top: 16px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px 6px;
        }

        th {
            background-color: #f1f5f9;
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

@php
    use Carbon\Carbon;
    use App\Models\AttendanceSetting;
    use App\Models\ClassRoom;

    $setting = AttendanceSetting::first();

    $startTime = Carbon::parse(
        $setting?->start_time ?? '07:00'
    )->format('H:i');

    $lateMinutes = (int) ($setting?->late_minutes ?? 0);

    $kelas = request('class_room_id')
        ? optional(ClassRoom::find(request('class_room_id')))->name ?? '-'
        : 'Semua Kelas';

    $periode = request('date')
        ? request('date')
        : (request('month') ? request('month') : 'Semua Tanggal');

    $grouped = $data->groupBy(
        fn ($a) => optional($a->students->classRoom)->name ?? 'Tanpa Kelas'
    );
@endphp

<h2>Laporan Absensi Siswa</h2>

<div class="meta">
    Kelas: <strong>{{ $kelas }}</strong> |
    Periode: <strong>{{ $periode }}</strong><br>
    Jam Masuk: <strong>{{ $startTime }}</strong> |
    Toleransi Telat: <strong>{{ $lateMinutes }} menit</strong>
</div>

@foreach ($grouped as $className => $rows)

<div class="class-title">Kelas {{ $className }}</div>

<table>
    <thead>
        <tr>
            <th style="width:12%">Tanggal</th>
            <th style="width:30%">Nama</th>
            <th style="width:14%">Jam</th>
            <th style="width:14%">Status</th>
            <th style="width:30%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)

            @php
                if ($row->date && $row->time_in) {
                    $startAt = Carbon::parse($row->date.' '.$startTime);
                    $lateLimit = $startAt->copy()->addMinutes($lateMinutes);
                    $scanTime = Carbon::parse($row->date.' '.$row->time_in);
                    $isLate = $scanTime->greaterThan($lateLimit);
                } else {
                    $isLate = false;
                }
            @endphp

            <tr>
                <td class="center">{{ $row->date }}</td>
                <td>{{ $row->students->name ?? '-' }}</td>
                <td class="center">{{ $row->time_in ?? '-' }}</td>
                <td class="center {{ $isLate ? 'late' : '' }}">
                    {{ $isLate ? 'TERLAMBAT' : 'HADIR' }}
                </td>
                <td>
                    {{ $isLate ? 'Datang melewati jam masuk' : '-' }}
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
