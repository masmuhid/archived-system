<?php

namespace App\Orchid\Screens\User;

use App\Models\Users\UserAudit;
use Orchid\Screen\Screen;
use DateTime;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Sight;
use Torann\GeoIP\Facades\GeoIP;


class UserAuditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'audits' => UserAudit::with('user')->latest()->paginate(20),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'User Access Logs';
    }

    public function description(): ?string
    {
        return 'List of user access. click on ID or User to show details';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('audits', [
                TD::make('id', __('ID'))
                    ->render(fn (UserAudit $audit) => ModalToggle::make($audit->id)
                        ->modal('myModal')
                        ->modalTitle(__('Details'))
                        ->asyncParameters([
                            'audit' => $audit->id,
                        ])
                    ),

                TD::make('user.name', __('User'))
                    ->render(function (UserAudit $audit) {
                        return ModalToggle::make($audit->user->name)
                            ->modal('myModal')
                            ->modalTitle(__('Details'))
                            ->asyncParameters([
                                'audit' => $audit->id,
                            ]);
                    }),
                

                TD::make('ip_address', __('IP'))
                    ->render(function (UserAudit $audit) {
                        return $audit->ip_address;
                    }),

                TD::make('location', __('Location'))
                    ->render(function (UserAudit $audit) {
                        $location = GeoIP::getLocation($audit->ip);
                        if($location->city == null && $location->country == null) {
                            return '-';
                        }
                        return $location->city . ', ' . $location->country;
                    }),
    

                TD::make('user_agent', __('Browser'))
                    ->render(function ($auth) {
                        $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($auth->user_agent));
                        return $agent->platform() . ' - ' . $agent->browser();
                    }),
    

                TD::make('event', __('Event'))
                    ->render(function (UserAudit $audit) {
                        return $audit->event;
                    }),

                TD::make('url', __('URL'))
                    ->render(function (UserAudit $audit) {
                        $url = $audit->url;
                
                        $parsedUrl = parse_url($url);
                        $baseUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
                        $path = explode('/', trim($parsedUrl['path'], '/'));
                        
                        return "{$baseUrl}/{$path[0]}/. . .";
                    }),

                TD::make('old_values', __('Old Values'))
                    ->render(function (UserAudit $audit) {
                        return Str::limit($audit->old_values, 50, '. . .}');
                    }),

                TD::make('new_values', __('New Values'))
                    ->render(function (UserAudit $audit) {
                        return Str::limit($audit->new_values, 50, '. . .}');
                    }),

                TD::make('created_at', __('Created at'))
                    ->render(function (UserAudit $audit) {
                        return (new DateTime($audit->created_at))->format('Y-m-d H:i:s');
                    }),
                
            ]),

            Layout::modal('myModal', 
                Layout::rows([
                    Input::make('audit.user_name')->title('User')->horizontal()->disabled(),
                    Input::make('audit.ip_address')->title('IP')->horizontal()->disabled(),
                    Input::make('audit.location')->title('Location')->horizontal()->disabled(),
                    Input::make('audit.browser')->title('Browser')->horizontal()->disabled(),
                    Input::make('audit.event')->title('Event')->horizontal()->disabled(),
                    Input::make('audit.url')->title('URL')->horizontal()->disabled(),

                    TextArea::make('audit.old_values')->title('Old Values')->rows(3)->horizontal()->disabled(),
                    TextArea::make('audit.new_values')->title('New Values')->rows(3)->horizontal()->disabled(),

                ])
            )
            ->size(Modal::SIZE_LG)
            ->withoutApplyButton()
            ->title('Details')
            ->async('asyncGetAudit'),
        ];
    }

    public function asyncGetAudit(UserAudit $audit): array
    {
        $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($audit->user_agent));
        $location = GeoIP::getLocation($audit->ip_address);
    
        return [
            'audit' => array_merge($audit->toArray(), [
                'user_name' => $audit->user->name ?? '-',
                'location' => ($location->city && $location->country) ? "{$location->city}, {$location->country}" : '-',
                'browser' => "{$agent->platform()} - {$agent->browser()}",
            ]),
        ];
    }
    
}
