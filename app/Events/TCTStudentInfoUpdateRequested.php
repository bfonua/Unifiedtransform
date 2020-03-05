<?php

namespace App\Events;

use Illuminate\Http\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TCTStudentInfoUpdateRequested
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $request;
    public $student_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, int $student_id)
    {
        $this->request = $request;
        $this->student_id = $student_id;
    }

    /*
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    // }
}
