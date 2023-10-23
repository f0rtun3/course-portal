<?php

namespace App\Events;

use App\Models\User;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, SerializesModels;

    public String $achievement_name;
    public User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($achievement_name, $user)
    {
        $this->achievement_name = $achievement_name;
        $this->user = $user;
    }

}
