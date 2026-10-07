<?php

return [
    'name' => env('ACADEMY_NAME', 'Wowz Martial Art'),

    /*
    |--------------------------------------------------------------------------
    | Admin sidebar navigation
    |--------------------------------------------------------------------------
    | Routes that do not exist yet render as disabled "Coming soon" items.
    */
    'navigation' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Website',
            'icon' => 'website',
            'children' => [
                ['label' => 'Services', 'route' => 'admin.services.index'],
                ['label' => 'Testimonials', 'route' => 'admin.testimonials.index'],
                ['label' => 'Our Team', 'route' => 'admin.team-members.index'],
                ['label' => 'Our Black Belts', 'route' => 'admin.black-belts.index'],
                ['label' => 'About Us', 'route' => 'admin.about.index'],
                ['label' => 'Gallery', 'route' => 'admin.gallery.index'],
                ['label' => 'Events', 'route' => 'admin.events.index'],
                ['label' => 'Achievements', 'route' => 'admin.achievements.index'],
            ],
        ],
        [
            'label' => 'Students',
            'icon' => 'students',
            'children' => [
                ['label' => 'Student List', 'route' => 'admin.students.index'],
                ['label' => 'Belts', 'route' => 'admin.belts.index'],
            ],
        ],
        [
            'label' => 'Belt Tests',
            'icon' => 'belt-tests',
            'children' => [
                ['label' => 'All Tests', 'route' => 'admin.belt-tests.index'],
                ['label' => 'Applications', 'route' => 'admin.belt-test-applications.index'],
                ['label' => 'Results', 'route' => 'admin.belt-test-results.index'],
                ['label' => 'Payments', 'route' => 'admin.payments.index'],
            ],
        ],
        [
            'label' => 'Certificates',
            'route' => 'admin.certificates.index',
            'icon' => 'certificates',
        ],
        [
            'label' => 'Competition Forms',
            'route' => 'admin.competition-forms.index',
            'icon' => 'competitions',
        ],
        [
            'label' => 'Contacts',
            'route' => 'admin.contacts.index',
            'icon' => 'contacts',
        ],
        [
            'label' => 'Notifications',
            'route' => 'admin.notifications.index',
            'icon' => 'notifications',
        ],
        [
            'label' => 'Audit Logs',
            'route' => 'admin.audit-logs.index',
            'icon' => 'audit',
        ],
    ],
];
