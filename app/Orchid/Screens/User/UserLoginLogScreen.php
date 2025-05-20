<?php

namespace App\Orchid\Screens\User;

use Orchid\Screen\Screen;
use App\Models\Users\UserLoginLog;
use DateTime;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class UserLoginLogScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'login_logs' => UserLoginLog::with('user')->latest()->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'User Login Logs';
    }

    public function description(): ?string
    {
        return 'List of user login logs';
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
            Layout::table('login_logs', [
                TD::make('id',__('#'))->render(function (UserLoginLog $log) {
                    return $log->id;
                }),

                TD::make('role', __('Role'))
                    ->render(function (UserLoginLog $log) {
                        return $log->role;
                    }),

                TD::make('user.name', __('User'))
                    ->render(function (UserLoginLog $log) {
                        return $log->user->name;
                    }),

                TD::make('browser', __('Browser'))
                    ->render(function (UserLoginLog $log) {
                        return $log->browser;
                    }),

                TD::make('ip_address', __('IP Address'))
                    ->render(function (UserLoginLog $log) {
                        return $log->ip_address;
                    }),

                TD::make('location', __('Location'))
                    ->render(function (UserLoginLog $log) {
                        return $log->location;
                    }),

                TD::make('login_at', __('Login at'))
                    ->render(function (UserLoginLog $log) {
                        return (new DateTime($log->login_at))->format('Y-m-d H:i:s');
                    }),

            ]),
        ];
    }
}
