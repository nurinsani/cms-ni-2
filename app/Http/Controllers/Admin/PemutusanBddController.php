<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PemutusanBddController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pemutusan BDD';
        return view('admin.pemutusan-bdd.index', $data);
    }
}
