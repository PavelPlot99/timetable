<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institute extends Model
{
    use HasFactory;
    protected $fillable = ['title'];
    public function groups():HasMany
    {
        return $this->hasMany(Group::class, 'institute_id');
    }
}
