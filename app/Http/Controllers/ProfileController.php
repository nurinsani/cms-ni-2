<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePassword(Request $request)
    {
        if (strlen($request->password) < 8) {
            return redirect()->back()->withErrors(['error' => 'Kata sandi minimal 8 angka.']);
        } else if ($request->password != $request->new_password) {
            return redirect()->back()->withErrors(['error' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.']);
        }

        $currentUser = Auth::user();
        $checkUser = DB::table('users')->where('id', $currentUser->id)->first();

        if (!Hash::check($request->old_password, $checkUser->password)) {
            return redirect()->back()->withErrors(['error' => 'Kata sandi lama tidak sesuai.']);
        }

        $newPassoword = Hash::make($request->password);

        DB::table('users')->where('id', $currentUser->id)->update([
            'password' => $newPassoword,
        ]);

        return redirect()->back()->with(['success' => 'Kata sandi berhasil diperbaharui.']);
    }

    public function updatePasswordPage()
    {
        $data = [
            'title' => 'Ganti Password'
        ];

        return view('profile.changePassword', $data);
    }
}
