<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPlanModule extends Model
{
    use HasFactory;

    protected $fillable = ['career_goal_id', 'judul_modul', 'deskripsi', 'urutan'];

    public function careerGoal()
    {
        return $this->belongsTo(CareerGoal::class, 'career_goal_id');
    }

    public function planCourses()
    {
        return $this->hasMany(LearningPlanCourse::class, 'module_id')->orderBy('urutan');
    }

    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            LearningPlanCourse::class,
            'module_id',
            'id',
            'id',
            'course_id'
        );
    }
}
