<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            Menu::make('Incoming Letters')
                ->icon('envelope-arrow-up')
                ->route('platform.letter.incoming')
                ->title('Navigation'),
                // ->badge(fn () => 6),

            Menu::make('Outcoming Letters')
                ->icon('envelope-arrow-down')
                ->route('platform.letter.outcoming'),
                
            Menu::make('Report')
                ->icon('file-earmark-post')
                ->route('platform.example'),

            Menu::make('Systems')
                ->icon('gear-wide-connected')
                ->title(__('Access rights'))
                ->badge(fn () => '+')
                ->permission('platform.systems.*')
                ->list([
                    Menu::make(__('Users'))
                        ->icon('people-fill')
                        ->route('platform.systems.users')
                        ->permission('platform.systems.users'),
    
                    Menu::make(__('Roles'))
                        ->icon('person-fill-gear')
                        ->route('platform.systems.roles')
                        ->permission('platform.systems.roles'),
        
                    Menu::make(__('Login Logs'))
                        ->icon('box-arrow-in-right')
                        ->route('platform.systems.login.logs')
                        ->permission('platform.systems.login.logs'),
        
                    Menu::make(__('Access Logs'))
                        ->icon('gear-wide-connected')
                        ->route('platform.systems.audits')
                        ->permission('platform.systems.audits'),
                ]),
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make(__('Profile'))
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission('platform.systems.audits', __('Audits'))
                ->addPermission('platform.systems.login.logs', __('Login Logs')),
                
        ];
    }
}
