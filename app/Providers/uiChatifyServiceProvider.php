<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class uiChatifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['template.navbar'], function ($view) {
            // $user = Auth::user();
            $user = null;

            if (Auth::guard('teacher')->check()) {
                $user = Auth::guard('teacher')->user();
            } elseif (Auth::guard('staff')->check()) {
                $user = Auth::guard('staff')->user();
            }

            $username = $user ? $user->username : 'Guest'; // Atau string default lainnya
            // dd($user);
            $unreadMessageCount = 0;
            $unreadMessages = [];

            if ($username) {
                // Menggunakan Query Builder untuk join tabel 'messages' dan 'users'
                $unreadMessages = DB::table('ch_messages')
                    ->join('users', 'ch_messages.to_id', '=', 'users.id')
                    ->join('users as sender', 'ch_messages.from_id', '=', 'sender.id')
                    ->where('users.email', $username)
                    ->where('ch_messages.seen', 0)
                    ->select('ch_messages.*', 'sender.name as sender_name')
                    ->get();

                // dd($unreadMessages);

                $unreadMessageCount = $unreadMessages->count();
            }

            $view->with([
                'hasUnreadMessages' => $unreadMessageCount > 0,
                'unreadMessageCount' => $unreadMessageCount,
                'unreadMessages' => $unreadMessages, // Tambahkan ini agar bisa menampilkan daftar pesan
            ]);
        });
    }
}
