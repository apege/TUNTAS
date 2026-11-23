<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai telah dibaca.
     */
    public function baca(Request $request, Notifikasi $notifikasi): JsonResponse|RedirectResponse
    {
        if ($notifikasi->penerima_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $notifikasi->markAsRead();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai telah dibaca.',
                'notifikasi_id' => $notifikasi->id,
            ]);
        }

        return back();
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca.
     */
    public function bacaSemua(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            Notifikasi::where('penerima_id', $user->id)
                ->whereNull('dibaca_at')
                ->update(['dibaca_at' => now()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai telah dibaca.',
            ]);
        }

        return back();
    }
}
