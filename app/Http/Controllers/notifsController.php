<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatRequest;
use Illuminate\Http\Request;
use App\Models\Cat;
use App\Models\Notif;
use App\Models\NotifUser;
use DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class notifsController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifs = Auth::user()->notifs;
        // return $notifs;'
        return view('dashboard.notifs.index', compact(['notifs']));

    }

    public function yajranotifs()
    {
        $notifs = Auth::user()->notifs;

        return DataTables::of($notifs)
        ->setRowData([
            'data-img' => function($notif) {
                $notefun = $notif->notefun;
                $class = ($notif->user_read_at != '')? ' readed fa fa-check-circle ' : '';
                $class .= ($notif->readed_at != '' && $notif->readed_by_user_id != Auth::id())? ' online ' : '';
                $readByOther = ($notif->readed_at != '' && $notif->readed_by_user_id != Auth::id())? '<span class="readByOther"></span>' : '';

                $html = '
                <a class="notiflink" notId="' .$notif->id .'"  href="'. route($notif->table_name .'.show', $notif->notifiable_id) .'">
                    <div class="user-img notifications yajranotifications"> <img
                        src="' .getSrc($notif->user_create, 'image') .'"
                        alt="user" class="img-circle yajraImg"> <span class="profile-status yajraReaded '. $class .'   pull-right"></span>
                        '. $readByOther .'
                    </div>
                </a>
                ';
                return new HtmlString($html);
            },

            'data-content' => function($notif) {
                $notefun = $notif->notefun;
                $html = '<a  class="notiflink" notId="' .$notif->id .'"   href="'. route($notif->table_name .'.show', $notif->notifiable_id) .'">'.$notif->$notefun().'</a>';
                return new HtmlString($html);
            },

            'data-create_user' => function($notif) {
                return new HtmlString($notif->user_create->fullName);
            },

            'data-created_at' => function($notif) {
                return new HtmlString(Carbon::parse($notif->created_at)->diffForHumans());
            }
        ])
        ->make(true);
    }

    public function notifsreaded(Notif $notif)
    {
        DB::beginTransaction();

        if($notif->readed_at == ""){
            $notif->update(['readed_by_user_id'=>Auth::id(),  'readed_at'=>Carbon::now()]);
        }

        if($notif->users && $notif->users->where('user_id', Auth::id())){
           NotifUser::where('user_id', Auth::id())->where('notif_id',$notif->id)->update(['readed_at'=>Carbon::now()]);
        }

        DB::commit();


    }

    public function realtimenotifs()
    {
        $data['notifs_html'] = Auth::user()->notifs_html();
        $data['notifsCount'] = Auth::user()->notifsCount();
        return$data;
    }
}


