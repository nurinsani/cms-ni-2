<?php

namespace App\Http\Controllers\It;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

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
        'role' => 'required',
        'password' => 'required|min:6',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Data User';
        $data['users'] = User::with(['branch', 'area', 'roles'])->latest('id')->get();
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
        $data['roles'] = Role::orderBy('name')->get();
        $data['branch'] = Branch::where('status_aktif', 'aktif')->orderBy('unit')->get();
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
        $validator = Validator::make($request->all(), $this->validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();
            $role = $data['role'];
            unset($data['role']);

            $data['password'] = bcrypt($request->password);

            DB::beginTransaction();
            $user = User::create($data);
            $user->generate_username();
            $user->syncRoles([$role]);
            DB::commit();

            Alert::success('Berhasil', 'Data user berhasil ditambahkan.');
            return redirect()->route('it.data-user.index');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            Alert::error('Gagal', 'Data user gagal dibuat.');
            return redirect()->back()->withInput();
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
        return redirect()->route('it.data-user.edit', $id);
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
        $data['roles'] = Role::orderBy('name')->get();
        $data['user'] = User::with('roles')->findOrFail($id);
        $data['cabang'] = Branch::where('status_aktif', 'aktif')->orderBy('unit')->get();
        if ($data['cabang']->isEmpty()) {
            $data['cabang'] = Branch::orderBy('unit')->get();
        }

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
        $rules = $this->validator;
        $rules['email'] = 'required|email|max:100|unique:users,email,' . $id;
        $rules['password'] = 'nullable|min:6';

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();
            $role = $data['role'];
            unset($data['role']);

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            } else {
                unset($data['password']);
            }

            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update($data);
            $user->generate_username();
            $user->syncRoles([$role]);
            DB::commit();

            Alert::success('Berhasil', 'Data user berhasil diperbarui.');
            return redirect()->route('it.data-user.index');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            Alert::error('Gagal', 'Data user gagal diperbarui.');
            return redirect()->back()->withInput();
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
        if (auth()->id() == $id) {
            Alert::error('Gagal', 'Anda tidak dapat menghapus akun yang sedang digunakan login.');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->syncRoles([]);
            $user->delete();
            DB::commit();

            Alert::success('Berhasil', 'Data user berhasil dihapus.');
            return redirect()->route('it.data-user.index');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            Alert::error('Gagal', 'Data user gagal dihapus.');
            return redirect()->back();
        }
    }
}
