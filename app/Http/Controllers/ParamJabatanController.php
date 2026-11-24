<?php

namespace App\Http\Controllers;

use App\Models\ParamJabatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ParamJabatanController extends Controller
{
    public function index(Request $request)
    {
        $rolePrefix = request()->is('akunting*') ? 'akunting' : 'admin';
        $title = 'Parameter Jabatan';

        if ($request->ajax()) {
            $data = ParamJabatan::orderBy('kategori', 'asc')->orderBy('nama_jabatan', 'asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kategori', function ($row) {
                    return $row->kategori ?: '<span class="text-muted">-</span>';
                })
                ->editColumn('status_aktif', function ($row) {
                    if ($row->status_aktif === 'aktif') {
                        return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Aktif</span>';
                    }
                    return '<span class="badge badge-secondary px-2 py-1"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-primary editJabatan mr-1" title="Edit Data"><i class="fas fa-edit mr-1"></i>Edit</button>';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-outline-danger hapusJabatan" title="Hapus Data"><i class="fas fa-trash-alt mr-1"></i>Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['kategori', 'status_aktif', 'action'])
                ->make(true);
        }

        return view('master.param-jabatan.index', compact('title', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jabatan'     => 'required|string|max:100',
            'kode_pembebanan'  => 'required|string|max:20',
            'kategori'         => 'nullable|string|max:100',
            'status_aktif'     => 'required|in:aktif,nonaktif',
            'keterangan'       => 'nullable|string',
        ], [
            'nama_jabatan.required'    => 'Nama jabatan wajib diisi.',
            'kode_pembebanan.required' => 'Kode pembebanan wajib diisi.',
            'status_aktif.required'    => 'Status wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        ParamJabatan::updateOrCreate(
            ['id' => $request->jabatan_id],
            [
                'nama_jabatan'    => $request->nama_jabatan,
                'kode_pembebanan' => $request->kode_pembebanan,
                'kategori'        => $request->kategori,
                'status_aktif'    => $request->status_aktif,
                'keterangan'      => $request->keterangan,
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Data Parameter Jabatan berhasil disimpan.'
        ]);
    }

    public function edit($id)
    {
        $jabatan = ParamJabatan::findOrFail($id);
        return response()->json($jabatan);
    }

    public function destroy($id)
    {
        $jabatan = ParamJabatan::findOrFail($id);
        $jabatan->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data Parameter Jabatan berhasil dihapus.'
        ]);
    }
}

