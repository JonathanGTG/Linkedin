<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseInclude extends Model
{
    protected $fillable = ['course_id', 'item'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

