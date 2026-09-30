<?php

namespace App\Filament\Pages;

use App\Filament\Support\Translatable;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Institutional data "À valider par The Invest In Africa Initiative":
 * key figures, history timeline, leadership (§5.1, §5.2).
 */
class InstitutionalContent extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?int $navigationSort = 0;

    protected function module(): string
    {
        return 'pages';
    }

    protected function settingKeys(): array
    {
        return ['impact', 'about'];
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.institutional');
    }

    public function getTitle(): string
    {
        return __('admin.institutional');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.sections.figures'))->description(__('admin.help.figures'))->schema([
                Repeater::make('impact.figures')->hiddenLabel()->reorderable()->maxItems(8)->collapsible()
                    ->itemLabel(fn (array $state) => ($state['value'] ?? '').($state['suffix'] ?? '').' — '.($state['label']['en'] ?? ''))
                    ->schema([
                        TextInput::make('value')->label(__('admin.fields.value'))->numeric()->required(),
                        TextInput::make('suffix')->label(__('admin.fields.suffix'))->maxLength(6)->placeholder('+, %, M'),
                        Translatable::text('label', __('admin.fields.label'), required: true)->columnSpanFull(),
                    ])->columns(2),
            ]),
            Section::make(__('admin.sections.timeline'))->description(__('admin.help.timeline'))->collapsible()->schema([
                Repeater::make('about.timeline')->hiddenLabel()->reorderable()->collapsible()
                    ->itemLabel(fn (array $state) => ($state['year'] ?? '').' — '.($state['title']['en'] ?? ''))
                    ->schema([
                        TextInput::make('year')->label(__('admin.fields.year'))->required()->maxLength(12),
                        Translatable::text('title', __('admin.fields.title'), required: true),
                        Translatable::textarea('text', __('admin.fields.description'), rows: 3),
                    ]),
            ]),
            Section::make(__('admin.sections.leadership'))->description(__('admin.help.leadership'))->collapsible()->schema([
                Repeater::make('about.leaders')->hiddenLabel()->reorderable()->collapsible()
                    ->itemLabel(fn (array $state) => $state['name'] ?? null)
                    ->schema([
                        TextInput::make('name')->label(__('admin.fields.name'))->required(),
                        TextInput::make('linkedin')->label('LinkedIn')->url(),
                        FileUpload::make('photo')->label(__('admin.fields.photo'))->image()->imageEditor()
                            ->imageAspectRatio('4:5')->disk('public')->directory('leaders')->maxSize(2048),
                        Translatable::text('role', __('admin.fields.role_title'), required: true),
                        Translatable::textarea('bio', __('admin.fields.bio'), rows: 4),
                    ]),
            ]),
        ]);
    }
}
