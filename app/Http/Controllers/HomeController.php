<?php

namespace App\Http\Controllers;

use App\Events\TransferEvt;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home()
    {
        // event(new TransferEvt("users_notif", "notif", "notif->notif_html()"));
        return redirect()->route('login');
    }
    
    public function testnotif($notif="notif Test")
    {
        event(new TransferEvt("users_notif", $notif, $notif));
        return $notif;
    }

    public function tables()
    {
        return view('dashboard.tables');
    }

    public function sendnotify()
    {
        return view('dashboard.sendnotify');
    }
    
    public function send_notification()
    {
        $tokens = UserToken::where('user_id', '>', 0)->groupby('token')->pluck('token')->toArray();
        // $tokens = json_encode($tokens);
        // return $tokens;
        return view('dashboard.send_notification', compact('tokens'));
        
    }
}
