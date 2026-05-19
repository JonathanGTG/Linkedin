<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPlanCourse extends Model
{
    use HasFactory;

    protected $fillable = ['module_id', 'course_id', 'urutan'];

    public function module()
    {
        return $this->belongsTo(LearningPlanModule::class, 'module_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
