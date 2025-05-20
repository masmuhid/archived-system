<?php

namespace App\Models\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAudit extends Model
{
    use HasFactory;

    protected $table = 'audits';

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();;
    }

}
