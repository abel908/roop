<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    use HasTranslatedLabel;

    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Translator = 'translator';
    case ProjectOfficer = 'project_officer';

    public static function translationGroup(): string
    {
        return 'role';
    }

    /**
     * Permission matrix — cahier des charges §8.3.
     * Modules: pages, translations, media, projects, submissions,
     * contact_messages, partners, seo, users, settings.
     */
    public function canManage(string $module): bool
    {
        return in_array($module, match ($this) {
            self::SuperAdmin => ['pages', 'translations', 'media', 'projects', 'submissions', 'contact_messages', 'partners', 'seo', 'users', 'settings'],
            self::Admin => ['pages', 'translations', 'media', 'projects', 'submissions', 'contact_messages', 'partners', 'seo', 'users'],
            self::Editor => ['pages', 'translations', 'media', 'partners', 'seo'],
            self::Translator => ['translations'],
            self::ProjectOfficer => ['media', 'projects', 'submissions', 'contact_messages'],
        }, true);
    }
}
