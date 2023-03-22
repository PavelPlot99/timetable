<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'institute_id',
        'course'
    ];
    public function users():HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    public function institute():BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }
}
