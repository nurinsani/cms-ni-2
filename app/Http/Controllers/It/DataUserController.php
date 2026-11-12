<?php

namespace App\Http\Controllers\It;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ApModel;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;

class DataUserController extends Controller
{
    public $validator = [
        'code_area' => 'required',
        'name' => 'required|string|max:50',
        'email' => 'required|email|max:100|unique:users,email',
        'nik' => 'required',
        'no_rek' => 'required',
        'nama_bank' => 'required',
        'atas_nama' => 'required',
        'password' => 'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Data User';
        $data['users'] = User::all();
        return view('it.data-user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Tambah User';
        $data['roles'] = Role::all();
        $data['branch'] = Branch::where('status_aktif', 'aktif')->get();
        return view('it.data-user.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), $this->validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $validator->validated();
            $data['password'] = bcrypt($request->password);
            DB::beginTransaction();
            $user = User::create($data);
            $user->generate_username();
            $user->assignRole($request->role);
            DB::commit();

            return redirect()->route('it.data-user.index')->with('success', 'Data berhasil dibuat');
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
        $data['title'] = 'Edit User';
        $data['roles'] = Role::all();
        $data['user'] = User::findOrFail($id);
        $data['cabang'] = Branch::all();
        
        return view('it.data-user.edit', $data);
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
        // dd($request->all());

        try {
            $this->validator['email'] = 'required|email|max:100|unique:users,email,' . $id;
            $this->validator['password'] = 'nullable';
            $validator = Validator::make($request->all(), $this->validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $validator->validated();
            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            } else {
                unset($data['password']);
            }
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update($data);
            $user->assignRole($request->role);
            DB::commit();

            return redirect()->route('it.data-user.index')->with('success', 'Data berhasil diubah');
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
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();

            return redirect()->route('it.data-user.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
