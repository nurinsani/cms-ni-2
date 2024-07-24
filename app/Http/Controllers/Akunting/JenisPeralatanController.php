<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Akunting\Peralatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Akunting\JenisPeralatan;
use App\Models\Akunting\ParamPeralatan;
use Illuminate\Support\Facades\Validator;

class JenisPeralatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Jenis Peralatan';
        $data['jenis_peralatan'] = JenisPeralatan::all();
        return view('akunting.jenis-peralatan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] =  'Tambah Jenis Peralatan';
        $data['param_peralatan'] = ParamPeralatan::all();
        return view('akunting.jenis-peralatan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'param_peralatan_id' => 'required',
                'nama_jenis' => 'required|string',
                'group_approval' => 'required',
            ]);

            DB::beginTransaction();
            $jenis_peralatan = New JenisPeralatan();
            $jenis_peralatan->param_peralatan_id = $request->param_peralatan_id;
            $jenis_peralatan->nama_jenis = $request->nama_jenis;
            $jenis_peralatan->group_approval = $request->group_approval;
            $jenis_peralatan->group_bdd = $request->group_bdd;
            $jenis_peralatan->group_inventaris = $request->group_inventaris;
            $jenis_peralatan->save();
            DB::commit();

            return redirect()->route('akunting.jenis-peralatan.index')->with('success', 'Data berhasil dibuat');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dibuat');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Jenis Peralatan';
        $data['param_peralatan'] = ParamPeralatan::all();
        $data['jenis_peralatan'] = JenisPeralatan::findOrFail($id);
        return view('akunting.jenis-peralatan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'param_peralatan_id' => 'required',
                'nama_jenis' => 'required|string',
                'group_approval' => 'required',
            ]);

            DB::beginTransaction();
            $jenis_peralatan = JenisPeralatan::find($id);
            $jenis_peralatan->param_peralatan_id = $request->param_peralatan_id;
            $jenis_peralatan->nama_jenis = $request->nama_jenis;
            $jenis_peralatan->group_approval = $request->group_approval;
            $jenis_peralatan->group_bdd = $request->group_bdd;
            $jenis_peralatan->group_inventaris = $request->group_inventaris;
            $jenis_peralatan->update();
            DB::commit();

            return redirect()->route('akunting.jenis-peralatan.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $jenis = JenisPeralatan::findOrFail($id);
            $jenis->delete();
            DB::commit();
            return redirect()->route('akunting.jenis-peralatan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function dropdown($id)
    {
        $jenis_peralatan = JenisPeralatan::where('param_peralatan_id', $id)->get();

        return response()->json($jenis_peralatan);
    }
}
