<?php

namespace App\Exports;

use App\Models\JurnalKp;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class jurnalKpExport implements FromView
{
    private $start_date;
    private $end_date;

    public function __construct($request)
    {
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
    }

    public function view(): View
    {
        if ($this->start_date && $this->end_date) {
            $jurnal_kp = JurnalKp::whereBetween('tanggal_transaksi', [$this->start_date, $this->end_date])
                ->get();
        } elseif ($this->start_date) {
            $jurnal_kp = JurnalKp::whereDate('tanggal_transaksi', '>=', $this->start_date)
                ->get();
        } elseif ($this->end_date) {
            $jurnal_kp = JurnalKp::whereDate('tanggal_transaksi', '<=', $this->end_date)
                ->get();
        } else {
            $jurnal_kp = JurnalKp::all();
        }

        $data = [
            'jurnal_kp' => $jurnal_kp,
        ];

        return view('exports.jurnalKp', $data);
    }
}
