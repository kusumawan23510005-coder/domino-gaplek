<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MatchHistory;
use App\Models\TopupHistory;

class GameController extends Controller
{
    // --- 1. LIHAT RIWAYAT GAME (Match History) ---
    public function history(Request $request)
    {
        $histories = MatchHistory::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Data riwayat game diambil',
            'data' => $histories
        ]);
    }

    // --- TAMBAHAN PENTING: LIHAT RIWAYAT QUEST/TOPUP ---
    // Tanpa ini, history quest tidak bisa tampil di HP
    public function getTopupHistory(Request $request)
    {
        $histories = TopupHistory::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data history quest/topup diambil',
            'data' => $histories
        ]);
    }

    // --- 2. HAPUS SEMUA RIWAYAT GAME ---
    public function clearHistory(Request $request)
    {
        $deleted = MatchHistory::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => "Berhasil menghapus $deleted riwayat pertandingan."]);
    }

    // --- 3. LOGIKA GAME (VS BOT / OFFLINE) ---
    public function submitResult(Request $request)
    {
        $request->validate(['result' => 'required|in:win,lose,draw']);

        $user = $request->user();
        $initialXp = $user->balance;

        if ($request->result == 'win') {
            $points = 100;
            $desc = "Menang VS Bot (+100 XP)";
        } elseif ($request->result == 'lose') {
            $points = 10;
            $desc = "Kalah VS Bot (+10 XP)";
        } else {
            $points = 25;
            $desc = "Seri VS Bot (+25 XP)";
        }

        $user->balance += $points;
        $user->save();

        // SIMPAN KE MATCH HISTORY
        MatchHistory::create([
            'user_id'   => $user->id,
            'result'    => $request->result,
            'xp_change' => $points,
            'desc'      => $desc
        ]);

        // LOGIKA QUEST
        $questMessage = "";
        if ($initialXp < 2000 && $user->balance >= 2000) {
            $bonus = 500;
            $user->balance += $bonus;
            $user->save();

            $questMessage = " | QUEST COMPLETED! (+500 XP)";

            // SIMPAN KE TOPUP HISTORY (Quest Reward)
            TopupHistory::create([
                'user_id'     => $user->id,
                'amount'      => $bonus,
                'method'      => 'quest_reward',
                'description' => "Achievement: Tembus 2000 XP"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Game Selesai. ' . $desc . $questMessage,
            'current_xp' => $user->balance
        ]);
    }
}
