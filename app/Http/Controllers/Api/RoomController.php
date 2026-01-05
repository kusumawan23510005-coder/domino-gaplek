<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    // 1. Create Room
    public function create(Request $request)
    {
        $user = $request->user();
        $roomCode = strtoupper(Str::random(4));

        $room = Room::create([
            'room_code' => $roomCode,
            'host_id'   => $user->id,
            'status'    => 'waiting',
            'board_cards' => [], // Array kosong langsung
            'host_hand' => [],
            'guest_hand' => [],
        ]);

        return response()->json([
            'message' => 'Room berhasil dibuat',
            'data'    => $room
        ]);
    }

    // 2. Join Room
    public function join(Request $request)
    {
        $request->validate(['room_code' => 'required|string']);
        $user = $request->user();
        $code = strtoupper($request->room_code);

        $room = Room::where('room_code', $code)->first();

        if (!$room) return response()->json(['message' => 'Room tidak ditemukan'], 404);
        if ($room->status !== 'waiting') return response()->json(['message' => 'Room penuh/main'], 400);
        if ($room->host_id == $user->id) return response()->json(['message' => 'Anda Hostnya'], 400);

        // --- BAGI KARTU ---
        $deck = [];
        for ($i = 0; $i <= 6; $i++) {
            for ($j = $i; $j <= 6; $j++) {
                $deck[] = ['top' => $i, 'bottom' => $j];
            }
        }

        shuffle($deck);

        $hostHand = array_splice($deck, 0, 7);
        $guestHand = array_splice($deck, 0, 7);

        $room->update([
            'guest_id'  => $user->id,
            'status'    => 'playing',
            'host_hand' => $hostHand,   // Langsung Array
            'guest_hand' => $guestHand, // Langsung Array
            'board_cards' => [],
            'current_turn_id' => $room->host_id,
            'pass_count' => 0
        ]);

        return response()->json(['message' => 'Berhasil masuk room', 'data' => $room]);
    }

    // 3. Detail
    public function detail($code)
    {
        $room = Room::where('room_code', $code)->with(['host', 'guest'])->first();
        if (!$room) return response()->json(['message' => 'Room hilang'], 404);
        return response()->json(['data' => $room]);
    }

    // 4. Get State (Polling)
    public function getState($code)
    {
        $room = Room::where('room_code', $code)->first();
        if (!$room) return response()->json(['message' => 'Room hilang'], 404);

        // TIDAK PERLU DECODE MANUAL LAGI
        // Karena sudah di-cast di Model, Laravel otomatis mengirimnya sebagai Array JSON yang benar

        return response()->json(['data' => $room]);
    }

    // --- 5. PLAY CARD (BERSIH) ---
    public function playCard($code, Request $request)
    {
        $request->validate([
            'card_index' => 'required|integer',
            'side'       => 'required|in:left,right'
        ]);

        $user = $request->user();
        $room = Room::where('room_code', $code)->first();

        if (!$room) return response()->json(['success' => false, 'message' => 'Room 404'], 404);
        if ($room->current_turn_id != $user->id) return response()->json(['success' => false, 'message' => 'Bukan giliranmu!'], 403);
        if ($room->status == 'finished') return response()->json(['success' => false, 'message' => 'Game selesai'], 400);

        // 1. Ambil Data (Langsung Array, HAPUS json_decode)
        // Gunakan operator ?? [] untuk keamanan jika data null
        $hostHand = $room->host_hand ?? [];
        $guestHand = $room->guest_hand ?? [];
        $boardCards = $room->board_cards ?? [];

        $isHost = ($room->host_id == $user->id);
        $myHand = $isHost ? $hostHand : $guestHand;

        // 2. Ambil Kartu
        $index = $request->card_index;
        if (!isset($myHand[$index])) {
            return response()->json(['success' => false, 'message' => 'Kartu tidak valid'], 400);
        }
        $cardToPlay = $myHand[$index];

        // 3. LOGIKA DOMINO (Sama seperti sebelumnya)
        if (empty($boardCards)) {
            $boardCards[] = $cardToPlay;
        } else {
            $leftEnd = $boardCards[0]['top'];
            $rightEnd = end($boardCards)['bottom'];

            if ($request->side == 'left') {
                if ($cardToPlay['bottom'] == $leftEnd) {
                    array_unshift($boardCards, $cardToPlay);
                } elseif ($cardToPlay['top'] == $leftEnd) {
                    $flipped = ['top' => $cardToPlay['bottom'], 'bottom' => $cardToPlay['top']];
                    array_unshift($boardCards, $flipped);
                } else {
                    return response()->json(['success' => false, 'message' => 'Angka tidak cocok di kiri!'], 400);
                }
            } else { // right
                if ($cardToPlay['top'] == $rightEnd) {
                    $boardCards[] = $cardToPlay;
                } elseif ($cardToPlay['bottom'] == $rightEnd) {
                    $flipped = ['top' => $cardToPlay['bottom'], 'bottom' => $cardToPlay['top']];
                    $boardCards[] = $flipped;
                } else {
                    return response()->json(['success' => false, 'message' => 'Angka tidak cocok di kanan!'], 400);
                }
            }
        }

        // 4. Update Tangan
        array_splice($myHand, $index, 1);

        // 5. Simpan ke Database (HAPUS json_encode)
        // Langsung masukkan variabel array
        $room->board_cards = $boardCards;
        if ($isHost) {
            $room->host_hand = $myHand;
        } else {
            $room->guest_hand = $myHand;
        }

        $room->pass_count = 0;

        // 7. Cek Menang
        if (empty($myHand)) {
            $room->winner_id = $user->id;
            $room->status = 'finished';
            $room->save();
            return response()->json(['success' => true, 'message' => 'You Win!', 'data' => $room]);
        }

        // 8. Ganti Giliran
        $room->current_turn_id = ($isHost) ? $room->guest_id : $room->host_id;
        $room->save();

        return response()->json(['success' => true, 'message' => 'Kartu masuk', 'data' => $room]);
    }


    // --- 6. LEWAT (PASS) (BERSIH) ---
    public function passTurn(Request $request, $code)
    {
        $user = $request->user();
        $room = Room::where('room_code', $code)->first();

        if (!$room) return response()->json(['success' => false, 'message' => 'Room 404'], 404);
        if ($room->current_turn_id != $user->id) return response()->json(['success' => false, 'message' => 'Bukan giliranmu'], 403);

        $room->pass_count += 1;

        if ($room->pass_count >= 2) {
            // Ambil data langsung (HAPUS json_decode)
            $hostHand = $room->host_hand ?? [];
            $guestHand = $room->guest_hand ?? [];

            $hostPoints = $this->calculateDots($hostHand);
            $guestPoints = $this->calculateDots($guestHand);

            if ($hostPoints < $guestPoints) {
                $room->winner_id = $room->host_id;
            } elseif ($guestPoints < $hostPoints) {
                $room->winner_id = $room->guest_id;
            } else {
                $room->winner_id = 0;
            }

            $room->status = 'finished';
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Game Gaple (Deadlock)!',
                'data' => $room
            ]);
        }

        $room->current_turn_id = ($room->current_turn_id == $room->host_id) ? $room->guest_id : $room->host_id;
        $room->save();

        return response()->json(['success' => true, 'message' => 'Anda Pass', 'data' => $room]);
    }

    private function calculateDots($hand)
    {
        $total = 0;
        if ($hand) {
            foreach ($hand as $card) {
                $total += ($card['top'] + $card['bottom']);
            }
        }
        return $total;
    }
}
