<?php

namespace App\Listeners;

use App\Classes\AchievementHandler;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;


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

        if(in_array($comment_unlocked_count, config('constants.COMMENT-ACHIEVEMENTS') )){
            $achievement_name = ($comment_unlocked_count == 1) ? "First Comment Written" : $comment_unlocked_count." Comments Written";
            AchievementUnlocked::dispatch($achievement_name, $user);
        }

        if(in_array($achievement_handler->total_unlocked_achievements, config('constants.BADGE-ACHIEVEMENTS'))) {
            $badge = $achievement_handler->check_badge_requirements();
            BadgeUnlocked::dispatch($badge["current_badge"], $user);
        }
    }

    public function handleLessonWatched(object $event): void
    {
        $lesson = $event->lesson;
        $user = $event->user;

        $comment_count = $user->comments()->count();
        $lesson_watched_count = $user->watched()->count();
        $achievement_handler = new AchievementHandler($lesson_watched_count, $comment_count);

        $lesson_count = $achievement_handler->get_lessons_unlocked();

        if(in_array($lesson_count, config('constants.LESSON-ACHIEVEMENTS') )){
            $achievement_name = ($lesson_count == 1) ? "First Lesson Watched" : $lesson." Lessons Watched";
            AchievementUnlocked::dispatch($achievement_name, $user);
        }

        if(in_array($achievement_handler->total_unlocked_achievements, config('constants.BADGE-ACHIEVEMENTS'))) {
            $badge = $achievement_handler->check_badge_requirements();
            BadgeUnlocked::dispatch($badge["current_badge"], $user);
        }
    }
}
