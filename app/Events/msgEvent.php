<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class msgEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user_image;
    public $user_name;
    public $user_id;
    public $sender_id;
    public $reciver_id;
    public $service_id;
    public $order_id;
    public $readed;
    public $content;
    public $created_at;
    public $files;

    public function __construct($data)
    {
        $this->user_image = $data->user->image;
        $this->user_name = $data->user->name;

        $this->user_id = $data->user_id;
        $this->sender_id = $data->sender_id;
        $this->reciver_id = $data->reciver_id;
        $this->service_id = $data->service_id;
        $this->order_id = $data->order_id;
        $this->readed = $data->readed;
        $this->content = $data->content;
        $this->created_at = '<span class="rtl">'.$data->created_at.'</span>';
        $this->files = "";

        if(isset($data->files)){
            $this->files .= '<ul class="px-0">';

            foreach ($data->files as $file):
            $this->files .= '<li class="d-inline-block mr-1">
                <a href="'. url('uploads/msgs/'.$file->name) .'" download>
                <i class="fas fa-paperclip"></i>
                '. $file->name .'
                </a>
            </li>';
            endforeach;

            $this->files .= '</ul><hr>';
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('msgEventChannel');
    }
}
