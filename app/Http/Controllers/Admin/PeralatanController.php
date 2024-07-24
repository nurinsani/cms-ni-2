<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PeralatanExport;
use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\Peralatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PeralatanController extends Controller
{
    public $validator = [

    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Daftar Peralatan';
        $data['daftar_peralatan'] = Peralatan::all();
        return view('admin.peralatan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Akunting\Peralatan  $peralatan
     * @return \Illuminate\Http\Response
     */
    public function show(Peralatan $peralatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Akunting\Peralatan  $peralatan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Akunting\Peralatan  $peralatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Akunting\Peralatan  $peralatan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $peralatan = Peralatan::findOrFail($id);
            $peralatan->delete();
            DB::commit();
            return redirect()->route('akunting.peralatan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function excel()
    {
        return Excel::download(new PeralatanExport, 'peralatan.xlsx');
    }
}
