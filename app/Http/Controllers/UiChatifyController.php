<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UiChatifyController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan from_id dan to_id dari parameter query string
        $fromId = $request->query('from_id');
        $toId = $request->query('to_id');

        // Mengambil semua user untuk mengisi dropdown
        $users = DB::table('users')->get();

        // Cek apakah kedua ID tersedia
        if ($fromId && $toId) {
            // Ambil pesan yang dikirim dari from_id ke to_id ATAU dari to_id ke from_id
            $historyChat = DB::table('ch_messages')
                ->where(function ($query) use ($fromId, $toId) {
                    $query->where('from_id', $fromId)
                        ->where('to_id', $toId);
                })
                ->orWhere(function ($query) use ($fromId, $toId) {
                    $query->where('from_id', $toId)
                        ->where('to_id', $fromId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            // Jika salah satu atau kedua ID tidak tersedia, kembalikan koleksi kosong.
            $historyChat = collect();
        }

        // Mengirim data yang telah difilter dan daftar user ke view
        return view('history-chat.index', compact('historyChat', 'users'));
    }
}
