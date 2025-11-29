<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TopupHistory;

class TopUpController extends Controller
{
    // ... (Fungsi topup, history, destroy BIARKAN TETAP ADA, jangan dihapus) ...
    // ... (Copy kode ini di paling bawah Class, sebelum kurung kurawal tutup '}') ...

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $user = $request->user(); 
        $user->balance += $request->amount;
        $user->save();

        TopupHistory::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'method' => 'button',
            'description' => 'Top Up Berhasil via API'
        ]);

        return response()->json([
            'message' => 'Top up berhasil',
            'balance' => $user->balance
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $histories = TopupHistory::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        return response()->json([
            'message' => 'List Riwayat Top Up',
            'data' => $histories
        ]);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $history = TopupHistory::find($id);
        if (!$history) return response()->json(['message' => 'Riwayat tidak ditemukan'], 404);
        if ($history->user_id != $user->id) return response()->json(['message' => 'Anda tidak berhak'], 403);
        $history->delete();
        return response()->json(['message' => 'Riwayat berhasil dihapus']);
    }

    // --- FUNGSI BARU: CATAT HASIL GAME ---
    public function gameResult(Request $request)
    {
        $request->validate([
            'result' => 'required|in:win,lose' // Android cuma boleh kirim 'win' atau 'lose'
        ]);

        $user = $request->user();
        $betAmount = 5000; // Taruhan tetap Rp 5.000

        if ($request->result == 'win') {
            $user->balance += $betAmount;
            $desc = "Menang Lawan Bot (+5000)";
        } else {
            $user->balance -= $betAmount;
            $desc = "Kalah Lawan Bot (-5000)";
        }

        $user->save();

        // Catat di history biar seru
        TopupHistory::create([
            'user_id' => $user->id,
            'amount' => ($request->result == 'win') ? $betAmount : -$betAmount,
            'method' => 'button',
            'description' => $desc
        ]);

        return response()->json([
            'message' => 'Saldo berhasil diupdate',
            'balance' => $user->balance
        ]);
    }
}