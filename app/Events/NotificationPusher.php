<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationPusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user_id;
    public $notification_id;

    /**
     * Create a new event instance.
     */
    public function __construct($notification_id, $user_id)
    {
        Log::info('NotificationPusher event initialized.', ['notification_id' => $notification_id, 'user_id' => $user_id]);

        $this->message = 'You have a new notification!';
        $this->notification_id = $notification_id;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['lumea'];
    }

    public function broadcastAs(): string
    {
        return 'notification-pusher';
    }
}