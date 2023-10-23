<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserActivity
{

    public function handleCommentWritten(object $event): void
    {
        //
    }

    public function handleLessonWatched(object $event): void
    {
        //
    }
}
