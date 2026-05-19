<?php
// app/Models/CareerGoal.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerGoal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'role_saat_ini', 'goal_title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modules()
    {
        return $this->hasMany(LearningPlanModule::class, 'career_goal_id')->orderBy('urutan');
    }

    // Hitung total progress dari semua course dalam plan
    public function getProgressPercentAttribute(): int
    {
        $courses = $this->modules->flatMap(fn($m) => $m->courses);
        $total   = $courses->count();
        if ($total === 0) return 0;

        $userId  = $this->user_id;
        $selesai = Enrollment::where('user_id', $userId)
            ->whereIn('course_id', $courses->pluck('id'))
            ->where('status', 'completed')
            ->count();

        return (int) round(($selesai / $total) * 100);
    }
}