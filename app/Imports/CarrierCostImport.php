<?php

namespace App\Imports;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CarrierCostImport implements ToModel, WithHeadingRow
{
    public $errors = [];
    private $current_row = 0;

    public function model(array $row)
    {
        try {
            $this->current_row++;

            $package = Package::where('tracking_number_out', $row['tracking_id'])->first();
            if ($package) {
                $package->update([
                    'xls_tracking_no' => $row['tracking_id'],
                    'xls_carrier_cost' => $row['net_charge_amount'],
                    'xls_carrier_cost_at' => Carbon::now(),
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(403, $th->getMessage() . " ERROR on EXCEL ROW #" . $this->current_row + 1);
        }
    }
}
