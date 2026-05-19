<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandsOnLab extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id','title','slug','description',
        'type','level','durasi_menit','teknologi','is_published',
    ];
    protected $casts = ['is_published' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }

    public function getDurasiFormatAttribute(): string
    {
        if ($this->durasi_menit < 60) return $this->durasi_menit . ' menit';
        $jam = intdiv($this->durasi_menit, 60);
        $men = $this->durasi_menit % 60;
        return $men > 0 ? "{$jam}j {$men}m" : "{$jam} jam";
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'practice'  => 'Hands-On Practice',
            'project'   => 'Project',
            'challenge' => 'Challenge',
            default     => $this->type,
        };
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
}
