<?php

namespace App\Support;

/**
 * Local fallback images when a record has no uploaded photo yet.
 */
class DemoMedia
{
    public static function service(?string $slug): string
    {
        return match ($slug) {
            'taekwondo' => asset('storage/services/taekwondo.jpg'),
            'kick-boxing' => asset('storage/services/kickboxing.jpg'),
            'kids-martial-arts' => asset('storage/services/kids.jpg'),
            'self-defence' => asset('storage/services/self-defence.jpg'),
            'competition-prep' => asset('storage/services/competition.jpg'),
            'yoga-recovery', 'yoga' => asset('storage/services/yoga.jpg'),
            default => asset('storage/services/taekwondo.jpg'),
        };
    }

    public static function team(int $index = 0): string
    {
        $photos = [
            asset('storage/team/arjun.jpg'),
            asset('storage/team/rohan.jpg'),
            asset('storage/team/priya.jpg'),
            asset('storage/team/meera.jpg'),
        ];

        return $photos[$index % count($photos)];
    }

    public static function blackBelt(int $index = 0): string
    {
        $photos = [
            asset('storage/black-belts/bb-1.jpg'),
            asset('storage/black-belts/bb-2.jpg'),
            asset('storage/black-belts/bb-3.jpg'),
            asset('storage/black-belts/bb-4.jpg'),
            asset('storage/team/arjun.jpg'),
            asset('storage/team/rohan.jpg'),
        ];

        return $photos[$index % count($photos)];
    }

    public static function event(): string
    {
        return asset('storage/events/open-house.jpg');
    }

    public static function gallery(int $index = 0): string
    {
        $photos = [
            asset('storage/gallery/evening-pads.jpg'),
            asset('storage/gallery/kids-stance.jpg'),
            asset('storage/gallery/district-floor.jpg'),
            asset('storage/gallery/grading-forms.jpg'),
            asset('storage/gallery/morning-fitness.jpg'),
            asset('storage/gallery/podium.jpg'),
        ];

        return $photos[$index % count($photos)];
    }
}
