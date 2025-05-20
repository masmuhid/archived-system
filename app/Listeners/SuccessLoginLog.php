<?php

namespace App\Listeners;

use App\Models\Users\UserLoginLog;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Request;
use Torann\GeoIP\Facades\GeoIP;

class SuccessLoginLog
{
    public function handle(Login $event)
    {
        // Inisialisasi Agent untuk mendapatkan OS dan browser
        $agent = new Agent();

        $browser = $agent->browser();
        $os = $agent->platform();

        // Format data menjadi "OS - Browser"
        $browserInfo = $os . ' - ' . $browser;

        $role = $event->user->role;
        $roleNames = $role->pluck('name')->implode(', ') ?: 'Admin';

        // Dapatkan IP address
        $ip = Request::ip();
        
        // Dapatkan informasi lokasi berdasarkan IP
        $locationData = GeoIP::getLocation($ip);
        $location = isset($locationData['city']) && isset($locationData['country']) 
                    ? $locationData['city'] . ', ' . $locationData['country'] 
                    : 'N/A';

        // Simpan log ke database
        UserLoginLog::create([
            'user_id' => $event->user->id,
            'role' => $roleNames,
            'browser' => $browserInfo, // Simpan OS dan browser yang sudah diformat
            'ip_address' => Request::ip(),
            'location' => $location,
            'login_at' => now(),
        ]);
    }
}