<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Akunting\ParamPeralatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ParamPeralatanController extends Controller
{
    public $validator = [
        'coa' => 'required|numeric|min:15',
        'nama' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Parameter Peralatan';
        $data['param_peralatan'] = ParamPeralatan::all();
        return view('akunting.param-peralatan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Tambah Parameter Peralatan';
        return view('akunting.param-peralatan.create', $data);
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
            $validator = Validator::make($request->all(), $this->validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $validator->validated();
            DB::beginTransaction();
            ParamPeralatan::create($data);
            DB::commit();

            return redirect()->route('akunting.param-peralatan.index')->with('success', 'Data berhasil dibuat');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dibuat');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ParamPeralatan  $paramPeralatan
     * @return \Illuminate\Http\Response
     */
    public function show(ParamPeralatan $paramPeralatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ParamPeralatan  $paramPeralatan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Parameter Peralatan';
        $data['peralatan'] = ParamPeralatan::findOrfail($id);
        return view('akunting.param-peralatan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParamPeralatan  $paramPeralatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), $this->validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $validator->validated();
            DB::beginTransaction();
            $peralatan = ParamPeralatan::findOrFail($id);
            $peralatan->update($data);
            DB::commit();

            return redirect()->route('akunting.param-peralatan.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParamPeralatan  $paramPeralatan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $peralatan = ParamPeralatan::findOrFail($id);
            $peralatan->delete();
            DB::commit();
            return redirect()->route('akunting.param-peralatan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
