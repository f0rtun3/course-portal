<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Classes\AchievementHandler;

use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $lessons_watched_count = $user->watched()->count();
        $comments_written_count = $user->comments()->count();

        $achievement_handler = new AchievementHandler($lessons_watched_count, $comments_written_count);
        $user_achievement_details = $achievement_handler->get_calculated_user_achievements();

        return response()->json([
            'unlocked_achievements' => $user_achievement_details["unlocked_achievements"],
            'next_available_achievements' => $user_achievement_details["next_available_achievements"],
            'current_badge' => $user_achievement_details["current_badge"],
            'next_badge' => $user_achievement_details["next_badge"],
            'remaining_to_unlock_next_badge' => $user_achievement_details["remaining_to_unlock_next_badge"],
        ]);
    }
}
