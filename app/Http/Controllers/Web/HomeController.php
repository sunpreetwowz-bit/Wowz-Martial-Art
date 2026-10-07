<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\Achievement;
use App\Models\BlackBelt;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('web.home', [
            'about' => AboutSection::query()
                ->active()
                ->whereIn('key', ['who_we_are', 'how_we_do', 'goals'])
                ->get()
                ->keyBy('key'),
            'services' => Service::query()->active()->orderBy('display_order')->take(6)->get(),
            'testimonials' => Testimonial::query()->approved()->orderBy('display_order')->take(3)->get(),
            'team' => TeamMember::query()->active()->orderBy('display_order')->take(4)->get(),
            'gallery' => GalleryImage::query()->active()->orderBy('display_order')->take(6)->get(),
            'achievements' => Achievement::query()
                ->published()
                ->latest('achieved_on')
                ->take(3)
                ->get(),
            'blackBelts' => BlackBelt::query()->active()->orderByDesc('promoted_on')->take(4)->get(),
            'nextEvent' => Event::query()
                ->published()
                ->whereDate('start_date', '>=', now()->toDateString())
                ->orderBy('start_date')
                ->first(),
        ]);
    }
}
