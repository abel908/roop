<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Settings module (§8.2): contact details, social networks, notification recipients, hero media. */
class SiteSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 9;

    protected function module(): string
    {
        return 'settings';
    }

    protected function settingKeys(): array
    {
        return ['contact', 'socials', 'notifications', 'hero'];
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.settings');
    }

    public function getTitle(): string
    {
        return __('admin.settings');
    }

    public function form(Schema $schema): Schema
    {
        $emails = fn (string $key, string $label) => TagsInput::make("notifications.$key")->label($label)
            ->placeholder('name@domain.org')->nestedRecursiveRules(['email']);

        return $schema->components([
            Section::make(__('admin.sections.contact'))->description(__('admin.help.contact'))->columns(2)->schema([
                TextInput::make('contact.email')->label(__('forms.labels.email'))->email(),
                TextInput::make('contact.phone')->label(__('forms.labels.phone'))->tel()->helperText(__('forms.help.phone')),
                Textarea::make('contact.address')->label(__('contact.details.address'))->rows(3),
                Textarea::make('contact.hours')->label(__('contact.details.hours'))->rows(3),
                TextInput::make('contact.lat')->label(__('admin.fields.latitude'))->numeric(),
                TextInput::make('contact.lng')->label(__('admin.fields.longitude'))->numeric(),
            ]),
            Section::make(__('admin.sections.socials'))->columns(3)->schema([
                TextInput::make('socials.linkedin')->label('LinkedIn')->url(),
                TextInput::make('socials.x')->label('X')->url(),
                TextInput::make('socials.facebook')->label('Facebook')->url(),
                TextInput::make('socials.instagram')->label('Instagram')->url(),
                TextInput::make('socials.youtube')->label('YouTube')->url(),
                TextInput::make('socials.wechat')->label('WeChat')->url(),
            ]),
            Section::make(__('admin.sections.notifications'))->description(__('admin.help.notifications'))->columns(2)->schema([
                $emails('submissions', __('admin.submissions')),
                $emails('interests', __('admin.interests')),
                $emails('contact_investment', __('admin.messages').' — '.__('enums.contact_subject.investment')),
                $emails('contact_project', __('admin.messages').' — '.__('enums.contact_subject.project')),
                $emails('contact_partnership', __('admin.messages').' — '.__('enums.contact_subject.partnership')),
                $emails('contact_press', __('admin.messages').' — '.__('enums.contact_subject.press')),
                $emails('contact_other', __('admin.messages').' — '.__('enums.contact_subject.other')),
            ]),
            Section::make(__('admin.sections.hero'))->description(__('admin.help.hero'))->columns(2)->schema([
                FileUpload::make('hero.video')->label(__('admin.fields.hero_video'))
                    ->acceptedFileTypes(['video/mp4'])->disk('public')->directory('hero')->maxSize(20480),
                FileUpload::make('hero.poster')->label(__('admin.fields.hero_poster'))
                    ->image()->imageEditor()->disk('public')->directory('hero')->maxSize(4096)
                    ->automaticallyResizeImagesToWidth('2400'),
            ]),
        ]);
    }
}
