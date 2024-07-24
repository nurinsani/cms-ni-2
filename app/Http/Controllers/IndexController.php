<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function redirect_role()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('akunting')) {
            return redirect()->route('akunting.dashboard');
        } elseif (auth()->user()->hasRole('admin-head')) {
            return redirect()->route('admin-head.dashboard');
        } elseif (auth()->user()->hasRole('finance')) {
            return redirect()->route('finance.dashboard');
        } elseif (auth()->user()->hasRole('pengurus')) {
            return redirect()->route('pengurus.dashboard');
        } elseif (auth()->user()->hasRole('it')) {
            return redirect()->route('it.dashboard');
        } elseif (auth()->user()->hasRole('atasan-akunting')) {
            return redirect()->route('atasan-akunting.dashboard');
        } elseif (auth()->user()->hasRole('atasan-it')) {
            return redirect()->route('atasan-it.dashboard');
        }
    }
}
