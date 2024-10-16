<?php

namespace App\Exports;

use App\Models\JurnalAp;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JurnalApExport implements FromView
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
            $jurnal_kp = JurnalAp::whereBetween('tanggal_transaksi', [$this->start_date, $this->end_date])
                ->get();
        } elseif ($this->start_date) {
            $jurnal_kp = JurnalAp::whereDate('tanggal_transaksi', '>=', $this->start_date)
                ->get();
        } elseif ($this->end_date) {
            $jurnal_kp = JurnalAp::whereDate('tanggal_transaksi', '<=', $this->end_date)
                ->get();
        } else {
            $jurnal_kp = JurnalAp::all();
        }

        $data = [
            'jurnal_kp' => $jurnal_kp,
        ];

        return view('exports.jurnal-ap', $data);
    }
}
