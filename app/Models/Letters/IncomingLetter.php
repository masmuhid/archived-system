<?php

namespace App\Models\Letters;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;

class IncomingLetter extends Model implements Auditable
{
    use HasFactory, AsSource, Filterable, Attachable, SoftDeletes, \OwenIt\Auditing\Auditable;

        protected $fillable = [
        'reference_number',
        'incoming_date',
        'sender',
        'file',
        'note',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $allowedSorts = [
        'reference_number',
        'incoming_date',
        'sender',
        'status',
        'updated_at',
    ];

    protected $allowedFilters = [
        'reference_number',
        'incoming_date',
        'sender',
        'status',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'file');
    }

}