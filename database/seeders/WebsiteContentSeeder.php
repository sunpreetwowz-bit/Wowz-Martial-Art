<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\EventStatus;
use App\Enums\PublishStatus;
use App\Models\AboutSection;
use App\Models\Achievement;
use App\Models\BlackBelt;
use App\Models\Event;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedServices();
        $this->seedAbout();
        $this->seedTeam();
        $this->seedBlackBelts();
        $this->seedTestimonials();
        $this->seedEvents();
        $this->seedAchievements();
        $this->seedGallery();
    }

    protected function seedServices(): void
    {
        // Hide old gym-style services that do not belong on a martial arts academy homepage
        Service::query()
            ->whereIn('slug', ['yoga', 'weight-gain-loss'])
            ->update(['status' => AccountStatus::Inactive]);

        $services = [
            [
                'name' => 'Taekwondo',
                'description' => "Olympic-style taekwondo for ages 6+. Learn kicks, poomsae (forms), sparring, and dojo etiquette with a clear white-to-black belt syllabus.\n\nClasses mix technical drilling with controlled partner work so students build power, balance, and timing without chaos on the floor. Monthly grading camps and personal coach feedback keep progress visible for parents and adult students alike.",
                'image' => ['url' => 'https://images.pexels.com/photos/6253299/pexels-photo-6253299.jpeg?auto=compress&cs=tinysrgb&w=1800', 'file' => 'services/taekwondo.jpg'],
            ],
            [
                'name' => 'Kids Martial Arts',
                'description' => "Ages 5–12 learn respect, balance, basic taekwondo kicks, and listening skills through games, partner drills, and short belt challenges.\n\nOur kids dojo keeps energy high and safety first — soft pads, clear rules, and coaches who know how to turn focus into a habit kids can use at school too.",
                'image' => ['url' => 'https://images.pexels.com/photos/7045577/pexels-photo-7045577.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'services/kids.jpg'],
            ],
            [
                'name' => 'Kick Boxing',
                'description' => "High-energy pad work, footwork, and conditioning for teens and adults. Build cardio, self-defence skills, and fight-ready coordination with controlled contact.\n\nExpect combination rounds, defence drills, and sweat — without the intimidation of a fight gym. Beginners are welcome; coaches scale intensity to your level.",
                'image' => ['url' => 'https://images.pexels.com/photos/4753990/pexels-photo-4753990.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'services/kickboxing.jpg'],
            ],
            [
                'name' => 'Self Defence',
                'description' => "Practical self-defence workshops covering awareness, escapes, and basic striking drawn from taekwondo and kickboxing.\n\nSuitable for beginners and women-only batches. We focus on simple skills you can remember under stress — create distance, strike effectively, and get to safety.",
                'image' => ['url' => 'https://images.pexels.com/photos/7045585/pexels-photo-7045585.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'services/self-defence.jpg'],
            ],
            [
                'name' => 'Competition Prep',
                'description' => "Invite-only coaching for district and state taekwondo events. Match strategy, timed sparring, weight guidance, and video review before tournaments.\n\nAthletes train with a clear event calendar, coach notes after each round, and support for registration paperwork so families know what to expect on competition day.",
                'image' => ['url' => 'https://images.pexels.com/photos/7045585/pexels-photo-7045585.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'services/competition.jpg'],
            ],
            [
                'name' => 'Yoga & Recovery',
                'description' => "Flexibility, breathing, and recovery sessions that support hard taekwondo training days — improve kick height, hip mobility, and cool-down after sparring.\n\nIdeal as a complement to kickboxing or belt prep: leave class looser, calmer, and ready for the next technical session.",
                'image' => ['url' => 'https://images.pexels.com/photos/6253299/pexels-photo-6253299.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'services/yoga.jpg'],
            ],
        ];

        foreach ($services as $index => $service) {
            $imagePath = $this->storeRemoteImage($service['image']['url'], $service['image']['file'], true);

            Service::query()->updateOrCreate(
                ['slug' => Str::slug($service['name'])],
                [
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'image_path' => $imagePath,
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }

        // Keep gym fitness lower if it already exists
        Service::query()->where('slug', 'gym-fitness')->update([
            'display_order' => 20,
            'status' => AccountStatus::Inactive,
        ]);
    }

    protected function seedAbout(): void
    {
        $about = [
            [
                'key' => 'who_we_are',
                'title' => 'Who we are',
                'content' => 'Wowz Martial Art is a Chandigarh taekwondo and martial arts academy in Sector 17. We train kids, teens, and adults in Olympic-style taekwondo, kickboxing, and self-defence — with belt grading, competition prep, and respectful coaching on every mat.',
            ],
            [
                'key' => 'how_we_do',
                'title' => 'How we train',
                'content' => 'Each class follows a clear plan: warm-up, taekwondo technique, partner drills or pad work, then cool-down. Belt tests are scheduled in advance, applications are tracked online, and coaches give honest feedback so progress never feels random.',
            ],
            [
                'key' => 'goals',
                'title' => 'Our goals',
                'content' => 'Build strong, confident martial artists — clean kicks, good manners, and the courage to keep showing up. We prepare students for belt exams and district taekwondo events while keeping safety first.',
            ],
            [
                'key' => 'mission',
                'title' => 'Mission',
                'content' => 'Deliver structured taekwondo and martial arts training that is accessible, safe, and inspiring for every age group in our community.',
            ],
            [
                'key' => 'vision',
                'title' => 'Vision',
                'content' => 'To be Chandigarh’s most trusted neighbourhood academy for taekwondo excellence and character development.',
            ],
            [
                'key' => 'philosophy',
                'title' => 'Training philosophy',
                'content' => 'Respect first. Progress next. Mastery comes from repetition, coaching feedback, and a supportive training family.',
            ],
        ];

        foreach ($about as $index => $section) {
            AboutSection::query()->updateOrCreate(
                ['key' => $section['key']],
                [
                    'title' => $section['title'],
                    'content' => $section['content'],
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }
    }

    protected function seedTeam(): void
    {
        $team = [
            [
                'name' => 'Coach Arjun Singh',
                'designation' => 'Head Instructor · 4th Dan Taekwondo',
                'biography' => 'National medallist with 12 years of coaching. Leads adult taekwondo classes, black-belt syllabus, and competition strategy.',
                'image' => ['url' => 'https://images.pexels.com/photos/4761792/pexels-photo-4761792.jpeg?auto=compress&cs=tinysrgb&w=800', 'file' => 'team/arjun.jpg'],
            ],
            [
                'name' => 'Sensei Rohan Das',
                'designation' => 'Kids Taekwondo Lead',
                'biography' => 'Specialises in ages 5–12. Makes discipline fun while building focus, balance, and safe falling skills.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045691/pexels-photo-7045691.jpeg?auto=compress&cs=tinysrgb&w=800', 'file' => 'team/rohan.jpg'],
            ],
            [
                'name' => 'Coach Priya Nair',
                'designation' => 'Kickboxing & Self Defence',
                'biography' => 'Former state-level striker. Runs pad circuits, defence drills, and women-only evening batches.',
                'image' => ['url' => 'https://images.pexels.com/photos/4753990/pexels-photo-4753990.jpeg?auto=compress&cs=tinysrgb&w=800', 'file' => 'team/priya.jpg'],
            ],
            [
                'name' => 'Coach Meera Kapoor',
                'designation' => 'Fitness & Recovery',
                'biography' => 'Sports science background. Designs strength blocks, mobility sessions, and return-to-train plans after minor injuries.',
                'image' => ['url' => 'https://images.pexels.com/photos/6295858/pexels-photo-6295858.jpeg?auto=compress&cs=tinysrgb&w=800', 'file' => 'team/meera.jpg'],
            ],
        ];

        foreach ($team as $index => $member) {
            $photoPath = $this->storeRemoteImage($member['image']['url'], $member['image']['file'], true);

            TeamMember::query()->updateOrCreate(
                ['name' => $member['name']],
                [
                    'designation' => $member['designation'],
                    'biography' => $member['biography'],
                    'photo_path' => $photoPath,
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }
    }

    protected function seedBlackBelts(): void
    {
        $members = [
            [
                'name' => 'Kabir Malhotra',
                'rank' => '1st Dan',
                'promoted_on' => now()->subYears(1)->subMonths(2)->toDateString(),
                'branch' => 'Intensity Martial Art and Fitness — Zirakpur',
                'biography' => 'Started as a shy teen white belt. Earned 1st Dan after consistent evening training and two district sparring seasons.',
                'image' => ['url' => 'https://images.pexels.com/photos/4761792/pexels-photo-4761792.jpeg?auto=compress&cs=tinysrgb&w=900', 'file' => 'black-belts/bb-1.jpg'],
            ],
            [
                'name' => 'Simran Kaur',
                'rank' => '1st Dan',
                'promoted_on' => now()->subMonths(10)->toDateString(),
                'branch' => 'Ramgharia Bhawan — Sector 27, Chandigarh',
                'biography' => 'Balanced college and belt prep for three years. Known for clean poomsae and mentoring younger students before grading day.',
                'image' => ['url' => 'https://images.pexels.com/photos/4753990/pexels-photo-4753990.jpeg?auto=compress&cs=tinysrgb&w=900', 'file' => 'black-belts/bb-2.jpg'],
            ],
            [
                'name' => 'Aarav Joshi',
                'rank' => '2nd Dan',
                'promoted_on' => now()->subYears(2)->toDateString(),
                'branch' => 'Kundan International School — Sector 46, Chandigarh',
                'biography' => 'Academy student since age 8. Promoted to 2nd Dan after competition prep clinics and strong examiner feedback.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045691/pexels-photo-7045691.jpeg?auto=compress&cs=tinysrgb&w=900', 'file' => 'black-belts/bb-3.jpg'],
            ],
            [
                'name' => 'Neha Bansal',
                'rank' => '1st Dan',
                'promoted_on' => now()->subMonths(5)->toDateString(),
                'branch' => 'Intensity Martial Art and Fitness — Zirakpur',
                'biography' => 'Adult pathway student who began for fitness and self-defence. Reached black belt through focused pad work and grading camps.',
                'image' => ['url' => 'https://images.pexels.com/photos/6295858/pexels-photo-6295858.jpeg?auto=compress&cs=tinysrgb&w=900', 'file' => 'black-belts/bb-4.jpg'],
            ],
        ];

        foreach ($members as $index => $member) {
            $photoPath = $this->storeRemoteImage($member['image']['url'], $member['image']['file'], true);

            BlackBelt::query()->updateOrCreate(
                ['name' => $member['name']],
                [
                    'rank' => $member['rank'],
                    'promoted_on' => $member['promoted_on'],
                    'branch' => $member['branch'],
                    'biography' => $member['biography'],
                    'photo_path' => $photoPath,
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }
    }

    protected function seedTestimonials(): void
    {
        $items = [
            [
                'author_name' => 'Rahul Sharma',
                'author_title' => 'Parent of yellow-belt taekwondo student',
                'content' => 'My son is more confident at school and looks forward to grading day. The taekwondo coaches are firm but kind.',
            ],
            [
                'author_name' => 'Ananya Verma',
                'author_title' => 'Adult taekwondo student',
                'content' => 'Clear belt steps, honest feedback, and great energy in every class. I finally understand my poomsae.',
            ],
            [
                'author_name' => 'Vikram Joshi',
                'author_title' => 'Kickboxing member',
                'content' => 'Best evening workout in Sector 17. Pads, footwork, and conditioning without ego on the floor.',
            ],
            [
                'author_name' => 'Sneha Kaur',
                'author_title' => 'Competition parent',
                'content' => 'The academy handled district taekwondo registration and kept us updated after every match. Felt professional.',
            ],
        ];

        foreach ($items as $index => $item) {
            Testimonial::query()->updateOrCreate(
                ['author_name' => $item['author_name']],
                [
                    'author_title' => $item['author_title'],
                    'content' => $item['content'],
                    'rating' => 5,
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                    'is_approved' => true,
                ]
            );
        }
    }

    protected function seedEvents(): void
    {
        $events = [
            [
                'slug' => 'open-house-training-day',
                'title' => 'Open House Taekwondo Day',
                'description' => "Tour the Zirakpur floor, meet coaches, and join a 45-minute beginner taekwondo session covering warm-ups, basic stances, and kicks.\n\nParents are welcome to watch. Coaches will answer belt pathway questions and suggest the right batch for kids, teens, or adults across our three branches.",
                'start_date' => now()->addDays(18)->toDateString(),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'venue' => 'Intensity Martial Art and Fitness',
                'address' => 'Intensity Martial Art and Fitness, Zirakpur',
                'registration_info' => 'Free entry. Message us with your preferred time slot — limited trial pads for first-timers.',
                'image' => ['url' => 'https://images.pexels.com/photos/4761792/pexels-photo-4761792.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'events/open-house.jpg'],
            ],
            [
                'slug' => 'yellow-belt-grading-camp',
                'title' => 'Yellow Belt Grading Camp',
                'description' => "Assessment day for white belts ready to grade. Morning block covers poomsae, kicks, and etiquette review; examiners announce results the same evening.\n\nStudents should arrive warmed up and in full dobok. Parents may wait in the designated seating area at Ramgharia Bhawan.",
                'start_date' => now()->addDays(35)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '14:00',
                'venue' => 'Ramgharia Bhawan',
                'address' => 'Ramgharia Bhawan, Sector 27, Chandigarh',
                'registration_info' => 'Apply through the student portal before the deadline. Fee covers examiner and certificate processing.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045576/pexels-photo-7045576.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'events/grading.jpg'],
            ],
            [
                'slug' => 'kids-parent-trial-morning',
                'title' => 'Kids & Parent Trial Morning',
                'description' => "A friendly introduction morning for ages 5–12 at our Sector 46 school campus. Kids try balance games, basic kicks, and listening drills while parents meet the kids coaching team.\n\nIdeal if you want to see the environment before joining a full kids martial arts batch.",
                'start_date' => now()->addDays(12)->toDateString(),
                'start_time' => '09:30',
                'end_time' => '11:30',
                'venue' => 'Kundan International School',
                'address' => 'Kundan International School, Sector 46, Chandigarh',
                'registration_info' => 'Free for new families. Reserve a slot via the contact form and mention “Kids trial — Sector 46”.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045577/pexels-photo-7045577.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'events/kids-trial.jpg'],
            ],
            [
                'slug' => 'summer-sparring-clinic',
                'title' => 'Summer Sparring Clinic',
                'description' => "Two-day controlled taekwondo sparring clinic for green belts and above. Focus on timing, distance, and safe contact rules with coach feedback after each round.\n\nHosted at our Zirakpur competition-ready floor with timed rounds and video notes for athletes preparing for district events.",
                'start_date' => now()->subDays(40)->toDateString(),
                'end_date' => now()->subDays(39)->toDateString(),
                'start_time' => '16:00',
                'end_time' => '19:00',
                'venue' => 'Intensity Martial Art and Fitness',
                'address' => 'Intensity Martial Art and Fitness, Zirakpur',
                'registration_info' => 'Completed — photos available in the gallery. Ask coaches about the next clinic date.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045570/pexels-photo-7045570.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'events/sparring.jpg'],
            ],
            [
                'slug' => 'women-self-defence-workshop',
                'title' => 'Women’s Self Defence Workshop',
                'description' => "A focused evening workshop on awareness, wrist releases, and simple striking drawn from taekwondo and kickboxing.\n\nOpen to beginners. No prior martial arts experience required — coaches keep the room supportive and practical.",
                'start_date' => now()->addDays(26)->toDateString(),
                'start_time' => '18:00',
                'end_time' => '20:00',
                'venue' => 'Ramgharia Bhawan',
                'address' => 'Ramgharia Bhawan, Sector 27, Chandigarh',
                'registration_info' => 'Limited seats. Enquire via contact form with subject “Self defence workshop”.',
                'image' => ['url' => 'https://images.pexels.com/photos/7045585/pexels-photo-7045585.jpeg?auto=compress&cs=tinysrgb&w=1400', 'file' => 'events/self-defence-workshop.jpg'],
            ],
        ];

        foreach ($events as $event) {
            $image = $event['image'];
            unset($event['image']);

            Event::query()->updateOrCreate(
                ['slug' => $event['slug']],
                array_merge($event, [
                    'image_path' => $this->storeRemoteImage($image['url'], $image['file'], true),
                    'status' => EventStatus::Published,
                ])
            );
        }
    }

    protected function seedAchievements(): void
    {
        $items = [
            [
                'title' => 'District Championship — Gold in Cadet Sparring',
                'description' => 'Academy taekwondo students secured gold in cadet under-45kg sparring with clean technique and strong sportsmanship.',
                'competition_name' => 'Chandigarh District Taekwondo Championship',
                'achievement_type' => 'Competition',
                'position' => 'Gold',
                'achieved_on' => now()->subMonths(2)->toDateString(),
            ],
            [
                'title' => 'State Poomsae Team — Silver',
                'description' => 'Synchronised forms team placed silver at the state invitational, showcasing months of pattern precision work.',
                'competition_name' => 'Punjab State Taekwondo Invitational',
                'achievement_type' => 'Competition',
                'position' => 'Silver',
                'achieved_on' => now()->subMonths(5)->toDateString(),
            ],
            [
                'title' => '100 Active Student Milestone',
                'description' => 'The academy crossed 100 active members across kids, teens, and adult taekwondo batches — a community we are proud of.',
                'competition_name' => 'Wowz Martial Art',
                'achievement_type' => 'Milestone',
                'position' => null,
                'achieved_on' => now()->subMonths(1)->toDateString(),
            ],
        ];

        foreach ($items as $item) {
            Achievement::query()->updateOrCreate(
                ['title' => $item['title']],
                array_merge($item, ['status' => PublishStatus::Published])
            );
        }
    }

    protected function seedGallery(): void
    {
        $categories = [
            ['name' => 'Classes', 'slug' => 'classes'],
            ['name' => 'Competitions', 'slug' => 'competitions'],
            ['name' => 'Belt Grading', 'slug' => 'belt-grading'],
        ];

        foreach ($categories as $index => $category) {
            GalleryCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }

        $images = [
            [
                'category' => 'classes',
                'title' => 'Taekwondo kick drill',
                'caption' => 'Adult batch practising high kicks and balance.',
                'url' => 'https://images.pexels.com/photos/4761792/pexels-photo-4761792.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/evening-pads.jpg',
            ],
            [
                'category' => 'classes',
                'title' => 'Kids taekwondo class',
                'caption' => 'Junior students learning stance and focus.',
                'url' => 'https://images.pexels.com/photos/7045691/pexels-photo-7045691.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/kids-stance.jpg',
            ],
            [
                'category' => 'competitions',
                'title' => 'Sparring practice',
                'caption' => 'Controlled sparring before district events.',
                'url' => 'https://images.pexels.com/photos/7045570/pexels-photo-7045570.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/district-floor.jpg',
            ],
            [
                'category' => 'belt-grading',
                'title' => 'Grading day forms',
                'caption' => 'Students performing poomsae for examiners.',
                'url' => 'https://images.pexels.com/photos/7045576/pexels-photo-7045576.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/grading-forms.jpg',
            ],
            [
                'category' => 'classes',
                'title' => 'Pad work round',
                'caption' => 'Kickboxing combinations on the pads.',
                'url' => 'https://images.pexels.com/photos/4753990/pexels-photo-4753990.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/morning-fitness.jpg',
            ],
            [
                'category' => 'competitions',
                'title' => 'Training focus',
                'caption' => 'Concentration before sparring rounds.',
                'url' => 'https://images.pexels.com/photos/6295858/pexels-photo-6295858.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'file' => 'gallery/podium.jpg',
            ],
        ];

        foreach ($images as $index => $image) {
            $category = GalleryCategory::query()->where('slug', $image['category'])->first();
            if (! $category) {
                continue;
            }

            $path = $this->storeRemoteImage($image['url'], $image['file'], true);
            if (! $path || ! Storage::disk('public')->exists($path)) {
                continue;
            }

            GalleryImage::query()->updateOrCreate(
                ['title' => $image['title']],
                [
                    'gallery_category_id' => $category->id,
                    'image_path' => $path,
                    'caption' => $image['caption'],
                    'display_order' => $index + 1,
                    'status' => AccountStatus::Active,
                ]
            );
        }
    }

    protected function storeRemoteImage(string $url, string $relativePath, bool $force = false): ?string
    {
        if (! $force && Storage::disk('public')->exists($relativePath)) {
            return $relativePath;
        }

        try {
            $response = Http::timeout(20)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 WowzMartialArtSeeder',
            ])->get($url);

            if (! $response->successful()) {
                return Storage::disk('public')->exists($relativePath) ? $relativePath : null;
            }

            Storage::disk('public')->put($relativePath, $response->body());

            return $relativePath;
        } catch (\Throwable) {
            return Storage::disk('public')->exists($relativePath) ? $relativePath : null;
        }
    }
}
