<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    protected Collection $data;
    protected int $rowCount = 0;

    protected string $startTime;
    protected int $lateMinutes;

    public function __construct(Collection $data)
    {
        $this->data = $data;

        $setting = AttendanceSetting::first();

        // Normalisasi jam masuk
        $this->startTime = Carbon::parse(
            $setting?->start_time ?? '07:00'
        )->format('H:i');

        $this->lateMinutes = (int) ($setting?->late_minutes ?? 0);
    }

    public function collection(): Collection
    {
        $mapped = $this->data->map(function ($a) {

            if (!$a->date || !$a->time_in) {
                return [
                    $a->date ?? '-',
                    $a->students->name ?? '-',
                    $a->students->classRoom->name ?? '-',
                    '-',
                    '-',
                    '-',
                ];
            }

            // Jam masuk hari itu
            $startAt = Carbon::parse($a->date . ' ' . $this->startTime);

            // Batas telat
            $lateLimit = $startAt->copy()->addMinutes($this->lateMinutes);

            // Waktu scan (AMAN: auto detect format)
            $scanTime = Carbon::parse($a->date . ' ' . $a->time_in);

            $isLate = $scanTime->greaterThan($lateLimit);

            return [
                $a->date,
                $a->students->name ?? '-',
                $a->students->classRoom->name ?? '-',
                $isLate ? 'TERLAMBAT' : 'HADIR',
                $scanTime->format('H:i:s'),
                $isLate ? 'Ya' : 'Tidak',
            ];
        });

        $this->rowCount = $mapped->count() + 2; // title + header

        return $mapped;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Jam Masuk',
            'Terlambat'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // === JUDUL ===
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'Laporan Absensi Siswa');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // === HEADER ===
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'E5E7EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // === BORDER ===
                $sheet->getStyle("A2:F{$this->rowCount}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // === ALIGNMENT ===
                $sheet->getStyle("A3:A{$this->rowCount}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("E3:E{$this->rowCount}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
