<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'slug', 'avatar', 'info', 'bio', 'profile_url', 'link'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor');
    }
}
