<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FingerprintSynced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student_id;
    public $fingerprint_id;

    public function __construct(Student $student)
    {
        $this->student_id = $student->id;
        $this->fingerprint_id = $student->fingerprint_id;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('fingerprint');
    }

    public function broadcastAs(): string
    {
        return 'FingerprintSynced';
    }
}
