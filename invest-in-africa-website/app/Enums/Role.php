<?php

namespace App\Enums;

use App\Models\Setting;
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

    public const MODULES = ['pages', 'translations', 'media', 'projects', 'submissions', 'contact_messages', 'partners', 'seo', 'users', 'settings'];

    /**
     * Default permission matrix — cahier des charges §8.3. It remains
     * configurable after launch (back-office "Roles and permissions").
     *
     * @return array<int, string>
     */
    public function defaultModules(): array
    {
        return match ($this) {
            self::SuperAdmin => self::MODULES,
            self::Admin => ['pages', 'translations', 'media', 'projects', 'submissions', 'contact_messages', 'partners', 'seo', 'users'],
            self::Editor => ['pages', 'translations', 'media', 'partners', 'seo'],
            self::Translator => ['translations'],
            self::ProjectOfficer => ['media', 'projects', 'submissions', 'contact_messages'],
        };
    }

    /** @return array<int, string> */
    public function modules(): array
    {
        if ($this === self::SuperAdmin) {
            return self::MODULES; // never lockable
        }

        $custom = Setting::get('permissions.'.$this->value);

        return is_array($custom) ? array_values(array_intersect($custom, self::MODULES)) : $this->defaultModules();
    }

    public function canManage(string $module): bool
    {
        return in_array($module, $this->modules(), true);
    }
}
