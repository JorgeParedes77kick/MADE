<?php
namespace App\Http\Controllers\Excel;

use App\Helpers\Debug;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles {
    protected $data;

    public function __construct($data) {
        $this->data = $data; // Asigna la colección dinámica
    }

    public function collection() {
        return $this->data; // Devuelve la colección dinámica
    }

    public function headings(): array {
        Debug::info($this->data->first());
        return array_keys($this->data->first()->toArray()); // Obtiene los nombres de las columnas automáticamente
    }
    public function styles(Worksheet $sheet) {
        $sheet->getStyle('1')->getFont()->setBold(true);
    }
}
