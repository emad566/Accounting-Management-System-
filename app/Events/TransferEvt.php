<?php

namespace App\Events;

use App\Models\UserToken;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferEvt implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $user_ids;
    public $notif_html;
    public $notif_view;

    public function __construct($users_notif=NULL, $notif, $notif_html)
    {
        if($users_notif){
            $user_ids = $users_notif->pluck('id');
        }else{
            $user_ids = $notif->users->pluck('id');
        }
        $this->user_ids = $user_ids;

        $this->notif_html = $notif_html;
        $this->notif_view = $notif->noteType . "_". $notif->notifiable_id;
        
        $tokens = UserToken::whereIn('user_id', $user_ids)->pluck('token')->toArray();
        
        // sendNotification(
        //     [
        //         'token'=>$tokens,
        //         'title'=>$notif->user_create->fullName,
        //         'body'=>$notif_html,
        //         'click_action'=>route( $notif->table_name.'.show', $notif->notifiable_id),
        //         'icon'=> getSrc($notif->user_create, 'image')
        //     ]
        // );

        
        // $this->user_ids = $user_ids;
        // $this->notif_html = $notif;
        // $this->notif_view = $notif;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('TransferEventChannel');
    }
}
