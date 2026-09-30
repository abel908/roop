<?php

namespace App\Providers\Filament;

use App\Filament\Support\InitialsAvatarProvider;
use App\Http\Middleware\SetAdminLocale;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Back-office (§8): professional, secure, simple. Palette of the institution,
 * two-factor authentication, French / English interface.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(isSimple: false)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable())
            ->brandName('The Invest In Africa Initiative')
            ->brandLogo(asset('images/logo-invest-in-africa.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('images/favicon.svg'))
            ->colors([
                'primary' => Color::hex('#0B9444'),
                'warning' => Color::hex('#FEC43F'),
                'danger' => Color::hex('#BF1E2D'),
                'gray' => Color::Neutral,
            ])
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make(fn () => __('admin.nav.requests')),
                NavigationGroup::make(fn () => __('admin.nav.opportunities')),
                NavigationGroup::make(fn () => __('admin.nav.content')),
                NavigationGroup::make(fn () => __('admin.nav.seo'))->collapsed(),
                NavigationGroup::make(fn () => __('admin.nav.administration'))->collapsed(),
            ])
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => view('filament.site-link'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetAdminLocale::class,
            ]);
    }
}
