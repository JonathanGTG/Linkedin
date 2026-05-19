<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id','category_id','title','slug','description','thumbnail',
        'instructor_name','durasi','level','durasi_detik','jumlah_learner','rating','rating_count',
        'scraped_level','release_date','source_url','is_published',
    ];
    protected $casts = ['is_published' => 'boolean', 'release_date' => 'date'];

    public function category()   { return $this->belongsTo(Category::class); }
    public function chapters()    { return $this->hasMany(Chapter::class)->orderBy('urutan'); }
    public function videos()      { return $this->hasMany(Video::class)->orderBy('urutan'); }
    public function skills()     { return $this->belongsToMany(Skill::class, 'course_skill'); }
    public function instructors(){ return $this->belongsToMany(Instructor::class, 'course_instructor'); }
    public function includes()   { return $this->hasMany(CourseInclude::class); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->whereIn('status', ['in_progress', 'completed']);
    }
    public function ratings()    { return $this->hasMany(CourseRating::class); }
    public function reviews()    { return $this->ratings(); }

    public function getDurasiFormatAttribute(): string
    {
        $jam   = intdiv($this->durasi_detik, 3600);
        $menit = intdiv($this->durasi_detik % 3600, 60);
        if ($jam > 0 && $menit > 0) return "{$jam}j {$menit}m";
        if ($jam > 0) return "{$jam}j";
        return "{$menit}m";
    }

    public function getLocalLearnerCountAttribute(): int
    {
        if (array_key_exists('local_learner_count', $this->attributes)) {
            return (int) $this->attributes['local_learner_count'];
        }

        return $this->activeEnrollments()->count();
    }

    public function getDisplayLearnerCountAttribute(): int
    {
        $localLearners = $this->local_learner_count;

        return $localLearners > 0 ? $localLearners : (int) $this->jumlah_learner;
    }

    public function getDisplayRatingAttribute(): float
    {
        $ratingCount = (int) ($this->attributes['user_rating_count'] ?? 0);
        if ($ratingCount > 0) {
            return round((float) $this->attributes['user_rating_avg'], 1);
        }

        return 0.0;
    }

    public function getDisplayRatingCountAttribute(): int
    {
        $ratingCount = (int) ($this->attributes['user_rating_count'] ?? 0);

        return $ratingCount > 0 ? $ratingCount : 0;
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeLevel($q, $l) { return $q->where('level', $l); }
    public function scopeShort($q)     { return $q->where('durasi_detik', '<=', 1800); }
    public function scopeWithLearningMetrics($q)
    {
        return $q
            ->withCount(['activeEnrollments as local_learner_count'])
            ->withCount(['ratings as user_rating_count'])
            ->withAvg('ratings as user_rating_avg', 'rating');
    }
}
