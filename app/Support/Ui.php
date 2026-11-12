<?php

namespace App\Support;

/**
 * Pictures shared by the home page and the dashboard: line icons (path data), filled two-tone
 * pictures (inner SVG), and each form template's picture and colour.
 */
class Ui
{
    public const ICONS = [
        'plus' => 'M12 5v14M5 12h14',
        'shield' => 'M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3Zm-3 9 2 2 4-4',
        'upload' => 'M12 15V4m0 0-4 4m4-4 4 4M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3',
        'clipboard' => 'M9 3h6a1 1 0 0 1 1 1v2H8V4a1 1 0 0 1 1-1Zm-1 2H6a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2M9 12h6M9 16h4',
        'layers' => 'M12 3 3 8l9 5 9-5-9-5ZM3 12l9 5 9-5M3 16l9 5 9-5',
        'users' => 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8',
        'search' => 'M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Zm9 2-4-4',
        'calendar' => 'M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'rows' => 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01',
        'tiles' => 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z',
        'edit' => 'M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4Z',
        'eye' => 'M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
        'bars' => 'M5 20V11M10 20V4M15 20v-8M20 20V8',
        'arrow' => 'M5 12h14m-5-5 5 5-5 5',
        'chevron' => 'm6 9 6 6 6-6',
    ];

    // Filled two-tone pictures (inner SVG) for the number cards and the templates.
    public const PEOPLE = '<circle cx="9" cy="8" r="3.6"/><path d="M2 20a7 7 0 0 1 14 0Z"/><circle cx="17" cy="8.5" r="3" opacity=".45"/><path d="M15.5 13.2A6 6 0 0 1 22 19.5h-4.5a8.5 8.5 0 0 0-2-6.3Z" opacity=".45"/>';

    public const PICTURES = [
        'clipboard' => '<rect x="4" y="4" width="16" height="18" rx="2.5"/><rect x="8" y="2" width="8" height="4.5" rx="1.5" fill="#fff" stroke="currentColor" stroke-width="1.6"/><path d="M8 11.5h8M8 15.5h5" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>',
        'layers' => '<path d="M12 2.5 2.5 7.5 12 12.5l9.5-5Z"/><path d="M4.4 11.1 2.5 12 12 17l9.5-5-1.9-.9L12 15.1Z" opacity=".7"/><path d="M4.4 15.6 2.5 16.5 12 21.5l9.5-5-1.9-.9L12 19.6Z" opacity=".45"/>',
        'users' => self::PEOPLE,
    ];

    // Each template's picture and colour on its card.
    public const TEMPLATE_LOOKS = [
        'blank' => ['<path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" opacity=".4"/><path d="M14 2v5h5Z" opacity=".7"/><circle cx="17.5" cy="17.5" r="5"/><path d="M17.5 15v5M15 17.5h5" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>', '#2f6fd6'],
        'household' => ['<path d="M12 3 2.5 11H5v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9h2.5Z"/><path d="M10 21v-6h4v6Z" fill="#fff" opacity=".85"/>', '#1f9d55'],
        'business' => ['<path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z" opacity=".4"/><path d="M3 4h18l1 5a3 3 0 0 1-5 2 3 3 0 0 1-5 0 3 3 0 0 1-5 0 3 3 0 0 1-5-2Z"/><path d="M8 4.5 7 10.5M12 4.5v6M16 4.5l1 6" stroke="#fff" stroke-width="1.4" opacity=".75"/><path d="M10 21v-5h4v5Z"/>', '#e8892b'],
        'listing' => ['<rect x="3" y="4.5" width="3.5" height="3.5" rx="1"/><rect x="3" y="10.25" width="3.5" height="3.5" rx="1"/><rect x="3" y="16" width="3.5" height="3.5" rx="1"/><rect x="9" y="5" width="12" height="2.5" rx="1.25" opacity=".5"/><rect x="9" y="10.75" width="12" height="2.5" rx="1.25" opacity=".5"/><rect x="9" y="16.5" width="12" height="2.5" rx="1.25" opacity=".5"/>', 'var(--tone-navy)'],
        'evaluation' => ['<path d="M12 1.5l2.4 1.7 2.9-.3 1.2 2.7 2.7 1.2-.3 2.9 1.7 2.4-1.7 2.4.3 2.9-2.7 1.2-1.2 2.7-2.9-.3L12 22.5l-2.4-1.7-2.9.3-1.2-2.7-2.7-1.2.3-2.9L1.5 12l1.7-2.4-.3-2.9 2.7-1.2 1.2-2.7 2.9.3Z"/><path d="M12 6.8l1.6 3.3 3.6.5-2.6 2.5.6 3.6-3.2-1.7-3.2 1.7.6-3.6-2.6-2.5 3.6-.5Z" fill="#fff" opacity=".92"/>', '#43a047'],
        'registration' => [self::PEOPLE, '#2f6fd6'],
    ];
}
