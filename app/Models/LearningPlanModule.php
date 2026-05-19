<?php
// app/Models/LearningPlanModule.php
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

    // Shortcut: langsung ambil Course objects
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

// ─────────────────────────────────────────────────
// app/Models/LearningPlanCourse.php
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