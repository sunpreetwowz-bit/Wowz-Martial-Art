<?php

namespace App\Support;

/**
 * Extra public content for each program page (beginner-friendly).
 */
class ProgramDetails
{
    public static function for(string $slug): array
    {
        $programs = [
            'taekwondo' => [
                'tagline' => 'Olympic-style kicking, forms, and sparring with a clear belt path.',
                'who_for' => 'Kids (6+), teens, and adults — from first white belt to black-belt prep.',
                'duration' => '60–75 minutes per class',
                'level' => 'Beginner to advanced',
                'learn' => [
                    'Basic stances, blocks, and front / roundhouse kicks',
                    'Poomsae (forms) for each belt rank',
                    'Controlled sparring and timing drills',
                    'Dojo etiquette, respect, and focus habits',
                    'Belt grading preparation and exam checklist',
                ],
                'benefits' => [
                    'Stronger legs, balance, and flexibility',
                    'Confidence for school and daily life',
                    'Clear progress through belt ranks',
                    'Path to district and state competition',
                ],
                'schedule' => [
                    ['batch' => 'Kids Taekwondo', 'days' => 'Mon · Wed · Fri', 'time' => '5:00 – 6:00 PM'],
                    ['batch' => 'Teens Taekwondo', 'days' => 'Tue · Thu · Sat', 'time' => '6:00 – 7:15 PM'],
                    ['batch' => 'Adult Taekwondo', 'days' => 'Mon · Wed · Fri', 'time' => '7:30 – 8:45 PM'],
                ],
                'cta' => 'Book a free taekwondo trial class',
            ],
            'kids-martial-arts' => [
                'tagline' => 'Fun, safe martial arts that build focus, respect, and confidence.',
                'who_for' => 'Children ages 5–12 who are new to martial arts or ready for belt challenges.',
                'duration' => '45–60 minutes per class',
                'level' => 'Beginner friendly',
                'learn' => [
                    'Listening skills and classroom respect',
                    'Basic kicks, punches, and balance games',
                    'Partner drills with soft pads',
                    'Safe falling and body awareness',
                    'Short belt challenges every term',
                ],
                'benefits' => [
                    'Better focus at school',
                    'Confidence without aggression',
                    'Fitness through play-based drills',
                    'Friendship and teamwork on the mat',
                ],
                'schedule' => [
                    ['batch' => 'Tiny Tigers (5–7)', 'days' => 'Tue · Thu', 'time' => '4:30 – 5:15 PM'],
                    ['batch' => 'Kids Martial Arts (8–12)', 'days' => 'Mon · Wed · Fri', 'time' => '5:00 – 6:00 PM'],
                ],
                'cta' => 'Book a kids trial class',
            ],
            'kick-boxing' => [
                'tagline' => 'High-energy pad work, footwork, and conditioning for teens and adults.',
                'who_for' => 'Teens and adults who want cardio, self-defence skills, and fight-ready coordination.',
                'duration' => '60–75 minutes per class',
                'level' => 'Beginner to intermediate',
                'learn' => [
                    'Jab, cross, hook, and kick combinations',
                    'Pad rounds and defence drills',
                    'Footwork, distance, and timing',
                    'Core conditioning and cool-down mobility',
                    'Safe contact rules and partner etiquette',
                ],
                'benefits' => [
                    'Serious calorie burn and cardio',
                    'Practical striking confidence',
                    'Stress relief after work or college',
                    'Strength without gym boredom',
                ],
                'schedule' => [
                    ['batch' => 'Kickboxing (Teens/Adults)', 'days' => 'Mon · Wed · Fri', 'time' => '7:30 – 8:45 PM'],
                    ['batch' => 'Women’s pad circuit', 'days' => 'Tue · Thu', 'time' => '6:30 – 7:30 PM'],
                ],
                'cta' => 'Join a kickboxing trial session',
            ],
            'self-defence' => [
                'tagline' => 'Practical awareness, escapes, and striking for real-world confidence.',
                'who_for' => 'Beginners, women-only batches, and anyone who wants simple, useful self-defence skills.',
                'duration' => '60 minutes per workshop / class',
                'level' => 'All levels',
                'learn' => [
                    'Situational awareness and personal space',
                    'Wrist releases and basic escapes',
                    'Palm strikes, knees, and low kicks',
                    'How to create distance and get to safety',
                    'Verbal boundary skills under pressure',
                ],
                'benefits' => [
                    'Practical skills you can remember under stress',
                    'Confidence walking alone or travelling',
                    'No prior martial arts experience needed',
                    'Supportive, respectful training environment',
                ],
                'schedule' => [
                    ['batch' => 'Open Self Defence', 'days' => 'Saturday', 'time' => '10:00 – 11:00 AM'],
                    ['batch' => 'Women’s Self Defence', 'days' => 'Thu', 'time' => '7:00 – 8:00 PM'],
                ],
                'cta' => 'Enquire about self-defence batches',
            ],
            'competition-prep' => [
                'tagline' => 'Invite-only coaching for district and state taekwondo events.',
                'who_for' => 'Green belts and above preparing for sparring or poomsae competitions.',
                'duration' => '75–90 minutes per session',
                'level' => 'Intermediate to advanced',
                'learn' => [
                    'Match strategy and point scoring habits',
                    'Timed sparring rounds with coach feedback',
                    'Poomsae precision for team and individual events',
                    'Weight management and recovery guidance',
                    'Pre-event checklist and mental prep',
                ],
                'benefits' => [
                    'Tournament-ready timing and distance',
                    'Video review of key rounds',
                    'Support with event registration paperwork',
                    'Pathway from academy mats to podium',
                ],
                'schedule' => [
                    ['batch' => 'Sparring clinic', 'days' => 'Sat', 'time' => '4:00 – 5:30 PM'],
                    ['batch' => 'Poomsae polish', 'days' => 'Sun', 'time' => '9:00 – 10:30 AM'],
                ],
                'cta' => 'Ask coaches about competition eligibility',
            ],
            'yoga-recovery' => [
                'tagline' => 'Flexibility, breathing, and recovery that support hard training days.',
                'who_for' => 'Martial artists and members who want mobility, kick height, and better recovery.',
                'duration' => '45–60 minutes per class',
                'level' => 'All levels',
                'learn' => [
                    'Hip openers for higher kicks',
                    'Breathing drills for calm focus',
                    'Post-sparring cool-down flows',
                    'Shoulder and back mobility',
                    'Simple routines you can repeat at home',
                ],
                'benefits' => [
                    'Fewer stiff mornings after hard classes',
                    'Better kick height and balance',
                    'Stress reset for mind and body',
                    'Complements taekwondo and kickboxing',
                ],
                'schedule' => [
                    ['batch' => 'Yoga & Recovery', 'days' => 'Saturday', 'time' => '8:00 – 9:00 AM'],
                ],
                'cta' => 'Book a recovery class',
            ],
        ];

        return $programs[$slug] ?? [
            'tagline' => 'Structured martial arts coaching at Wowz Martial Art, Chandigarh.',
            'who_for' => 'Students of all ages looking for disciplined, safe training.',
            'duration' => '60 minutes per class',
            'level' => 'All levels',
            'learn' => [
                'Core techniques for this program',
                'Partner drills with coach supervision',
                'Fitness and mobility work',
                'Respect, focus, and dojo etiquette',
            ],
            'benefits' => [
                'Fitness and coordination',
                'Confidence and discipline',
                'Clear coaching feedback',
                'Supportive academy community',
            ],
            'schedule' => [
                ['batch' => 'Ask reception', 'days' => 'Mon–Sat', 'time' => 'See current batch sheet'],
            ],
            'cta' => 'Enquire about this program',
        ];
    }
}
