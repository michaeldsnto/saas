<?php

namespace App\Exports\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class InventoryReportExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
