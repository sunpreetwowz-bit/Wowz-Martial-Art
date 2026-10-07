<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Achievement;

class AchievementController extends Controller
{
    public function index()
    {
        return view('web.achievements.index', [
            'achievements' => Achievement::query()
                ->published()
                ->with('student.user')
                ->latest('achieved_on')
                ->paginate(12),
        ]);
    }
}
