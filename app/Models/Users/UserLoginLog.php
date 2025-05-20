<?php

namespace App\Models\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'login_logs';

    protected $fillable = [
        'user_id', 
        'role', 
        'browser', 
        'ip_address', 
        'location',
        'login_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
