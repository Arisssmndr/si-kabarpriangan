<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use App\Models\PembayaranIklan;
use App\Models\SistemConfig;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $now = now();

        $orderKoranUser = TransaksiKoran::where('input_oleh_user_id', $user->id)
            ->whereMonth('tanggal_transaksikoran', $now->month)
            ->whereYear('tanggal_transaksikoran', $now->year)
            ->count();

        $kwitansiUser = PembayaranIklan::where('kasir_user_id', $user->id)
            ->whereMonth('tanggal_bayar', $now->month)
            ->whereYear('tanggal_bayar', $now->year)
            ->count();

        $totalOrderDivisi = TransaksiKoran::whereMonth('tanggal_transaksikoran', $now->month)->whereYear('tanggal_transaksikoran', $now->year)->count()
            + TransaksiOnline::whereMonth('tanggal_transaksionline', $now->month)->whereYear('tanggal_transaksionline', $now->year)->count()
            + TransaksiPriangan::whereMonth('tanggal_transaksipriangan', $now->month)->whereYear('tanggal_transaksipriangan', $now->year)->count();

        $stats = [
            'order_diinput'    => $orderKoranUser,
            'kwitansi_dicatat' => $kwitansiUser,
            'order_divisi'     => $totalOrderDivisi,
            'jam_cutoff'       => SistemConfig::getJamCutoff(),
            'ppn_persen'       => SistemConfig::getPpnPersen(),
        ];

        $recentLogs = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('profile.edit', [
            'user'       => $user,
            'stats'      => $stats,
            'recentLogs' => $recentLogs,
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
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
