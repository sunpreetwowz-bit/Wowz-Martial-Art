<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\TeamMember;

class AboutController extends Controller
{
    public function index()
    {
        return view('web.about', [
            'sections' => AboutSection::query()->active()->orderBy('display_order')->get(),
            'team' => TeamMember::query()->active()->orderBy('display_order')->get(),
        ]);
    }

    public function team()
    {
        return view('web.team', [
            'team' => TeamMember::query()->active()->orderBy('display_order')->get(),
        ]);
    }
}
