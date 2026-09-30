<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Languages — cahier des charges §7.1
    |--------------------------------------------------------------------------
    | Keys are the URL prefixes (/en/, /fr/, /zh/). "hreflang" is the value
    | declared in <html lang> and hreflang annotations. Labels are always
    | written in their own language, without flags.
    */
    'locales' => [
        'en' => ['label' => 'English', 'hreflang' => 'en', 'icu' => 'en', 'og' => 'en_US'],
        'fr' => ['label' => 'Français', 'hreflang' => 'fr', 'icu' => 'fr', 'og' => 'fr_FR'],
        'zh' => ['label' => '中文', 'hreflang' => 'zh-Hans', 'icu' => 'zh_Hans', 'og' => 'zh_CN'],
    ],

    'default_locale' => env('SITE_DEFAULT_LOCALE', 'en'),

    'name' => 'The Invest In Africa Initiative',

    /*
    | Brand palette — extracted from the official logo (§4.2).
    */
    'colors' => [
        'green' => '#0B9444',
        'green_deep' => '#08703A',
        'gold' => '#FEC43F',
        'red' => '#BF1E2D',
        'black' => '#000000',
        'white' => '#FFFFFF',
    ],

    /*
    | Submit a Project uploads (§6.3) — proposal: 5 files of 10 MB.
    */
    'uploads' => [
        'max_files' => (int) env('UPLOAD_MAX_FILES', 5),
        'max_kb' => (int) env('UPLOAD_MAX_KB', 10240),
        'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'],
        'mimetypes' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', // OOXML files are sometimes detected as zip archives
            'image/jpeg',
            'image/png',
        ],
        'description_max' => (int) env('SUBMISSION_DESCRIPTION_MAX', 3000),
        // Optional ClamAV binary for antivirus scanning ("clamdscan" or "clamscan").
        'antivirus_binary' => env('ANTIVIRUS_BINARY'),
    ],

    /*
    | Anti-spam (§6.3, §11.1) — China-compatible, no reCAPTCHA.
    */
    'antispam' => [
        'honeypot_field' => 'website',
        'min_seconds' => (int) env('FORM_MIN_SECONDS', 4),
    ],

    'currencies' => ['USD', 'EUR', 'XOF', 'XAF', 'NGN', 'KES', 'ZAR', 'MAD', 'EGP', 'GHS', 'CNY', 'GBP'],

    /*
    | African countries grouped by region, for catalogue filters.
    */
    'africa' => [
        'north' => ['DZ', 'EG', 'LY', 'MA', 'MR', 'SD', 'TN', 'EH'],
        'west' => ['BJ', 'BF', 'CV', 'CI', 'GM', 'GH', 'GN', 'GW', 'LR', 'ML', 'NE', 'NG', 'SN', 'SL', 'TG'],
        'central' => ['AO', 'CM', 'CF', 'TD', 'CG', 'CD', 'GQ', 'GA', 'ST'],
        'east' => ['BI', 'KM', 'DJ', 'ER', 'ET', 'KE', 'MG', 'MW', 'MU', 'RW', 'SC', 'SO', 'SS', 'TZ', 'UG'],
        'southern' => ['BW', 'SZ', 'LS', 'MZ', 'NA', 'ZA', 'ZM', 'ZW'],
    ],

    'analytics' => [
        'ga4_id' => env('GA4_MEASUREMENT_ID'),
    ],

];
