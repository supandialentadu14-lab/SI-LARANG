<?php

namespace App\Http\Controllers;

use App\Models\OpdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OpdController extends Controller
{
    public function index(): View
    {
        $items = OpdSetting::where('user_id', Auth::id())->orderByDesc('updated_at')->get();
        return view('settings.opd_list', compact('items'));
    }

    public function edit(): View
    {
        $setting = OpdSetting::where('user_id', Auth::id())->first();
        if (! $setting) {
            $setting = OpdSetting::create(['user_id' => Auth::id()]);
        }
        return view('settings.opd', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_opd' => 'nullable|string|max:255',
            'alamat_opd' => 'nullable|string|max:500',
            'kepala_nama' => 'nullable|string|max:255',
            'kepala_pangkat' => 'nullable|string|max:255',
            'kepala_jabatan' => 'nullable|string|max:255',
            'kepala_nip' => 'nullable|string|max:50',
            'pengurus_nama' => 'nullable|string|max:255',
            'pengurus_pangkat' => 'nullable|string|max:255',
            'pengurus_jabatan' => 'nullable|string|max:255',
            'pengurus_nip' => 'nullable|string|max:50',
            'pengguna_nama' => 'nullable|string|max:255',
            'pengguna_pangkat' => 'nullable|string|max:255',
            'pengguna_jabatan' => 'nullable|string|max:255',
            'pengguna_nip' => 'nullable|string|max:50',
        ]);
        $setting = OpdSetting::where('user_id', Auth::id())->first();
        if (! $setting) {
            $setting = OpdSetting::create(array_merge($validated, ['user_id' => Auth::id()]));
        } else {
            $setting->update($validated);
        }
        return redirect()->route('settings.opd.index')->with('success', 'Data OPD tersimpan.');
    }
}
