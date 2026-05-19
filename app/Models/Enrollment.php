<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','course_id','status','enrolled_at','completed_at','last_activity_at'];
    protected $casts = ['enrolled_at' => 'datetime', 'completed_at' => 'datetime', 'last_activity_at' => 'datetime'];

    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }

    public function getProgressPercentAttribute(): int
    {
        if (!$this->course) return 0;
        $total = $this->course->videos()->count();
        if ($total === 0) return 0;
        $selesai = VideoProgress::where('user_id', $this->user_id)
            ->whereIn('video_id', $this->course->videos->pluck('id'))
            ->where('is_completed', true)->count();
        return (int) round(($selesai / $total) * 100);
    }
}
