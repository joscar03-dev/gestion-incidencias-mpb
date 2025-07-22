<?php

namespace App\Providers\Filament;

use App\Filament\Resources\TicketResource\Widgets\TicketsTiempoResolucionChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Kirschbaum\Commentions\CommentionsPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Navigation\UserMenuItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->databaseNotifications(true) //actiar notificaciones de base de datos
            ->databaseNotificationsPolling(null)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Purple,
            ])
            ->plugins([
                CommentionsPlugin::make(),
                FilamentApexChartsPlugin::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                TicketsTiempoResolucionChart::class,
                \App\Filament\Widgets\ItilOverviewWidget::class,
                \App\Filament\Widgets\ItilCategoryDistributionWidget::class,
                \App\Filament\Widgets\ItilTrendAnalysisWidget::class,
                \App\Filament\Widgets\ItilWorkloadTableWidget::class,
                \App\Filament\Widgets\ItilIncidentMetricsChart::class,
                \App\Filament\Widgets\ItilSlaComplianceChart::class,
                \App\Filament\Widgets\ItilCategoryStatsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot()
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                MenuItem::make()
                    ->label('Cambiar vista')
                    ->url(fn () => request()->is('admin*') ? url('/dashboard') : url('/admin'))
                    ->icon('heroicon-o-user-group')
                    ->visible(fn () => auth()->user()?->hasRole(['Super Admin', 'Admin'])),
            ]);
        });
    }
}
