<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TopUpController extends Controller
{
    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $user = $request->user(); // user yang sedang login

        // Tambah saldo
        $user->balance += $request->amount;
        $user->save();

        return response()->json([
            'message' => 'Top up berhasil',
            'balance' => $user->balance
        ]);
    }
}
