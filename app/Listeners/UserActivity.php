<?php

namespace App\Listeners;

use App\Classes\AchievementHandler;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserActivity
{

    public function handleCommentWritten(object $event): void
    {
        $comment = $event->comment;
        $user = $comment->user();
        $comment_count = $user->comments()->count();
        $lesson_watched_count = $user->watched()->count();
        $achievement_handler = new AchievementHandler($lesson_watched_count, $comment_count);

        $comment_unlocked_count = $achievement_handler->get_comments_unlocked();

        if(in_array($comment_unlocked_count, config('constants.LESSON-ACHIEVEMENTS') )){
            $achievement_name = ($comment_unlocked_count == 1) ? "First Comment Written" : $comment_unlocked_count." Comments Written";
            AchievementUnlocked::dispatch($achievement_name, $user);
        }

        if(in_array($achievement_handler->total_unlocked_achievements, config('constants.LESSON-ACHIEVEMENTS'))) {
            $badge = $achievement_handler->check_badge_requirements();
            BadgeUnlocked::dispatch($badge["current_badge"], $user);
        }
    }

    public function handleLessonWatched(object $event): void
    {

    }
}
