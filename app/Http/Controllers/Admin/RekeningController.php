<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
     
        $title = 'Daftar Rekening';
        if ($request->ajax()) {
  
            $data = Rekening::latest()->get();
  
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
   
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('admin.rekening.index', ['title' => $title]);
    }

    public function store(Request $request)
    {
        Rekening::updateOrCreate([
                    'id' => $request->rekening_id
                ],
                [
                    'no_rek' => $request->no_rek, 
                    'nama_bank' => $request->nama_bank,
                    'atas_nama' => $request->atas_nama

                ]);        
     
        return response()->json(['success'=>'Product saved successfully.']);
    }

    public function edit($id)
    {
        $rekening = Rekening::find($id);
        return response()->json($rekening);
    }

    public function destroy($id)
    {
        Rekening::find($id)->delete();
      
        return response()->json(['success'=>'Product deleted successfully.']);
    }
}
