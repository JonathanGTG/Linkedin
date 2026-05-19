<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id','course_id','chapter_id','title','slug','video_url',
        'durasi_detik','urutan','tipe','is_preview',
    ];
    protected $casts = ['is_preview' => 'boolean'];

    public function course()  { return $this->belongsTo(Course::class); }
    public function chapter() { return $this->belongsTo(Chapter::class); }
    public function media()   { return $this->belongsToMany(MediaAsset::class, 'lesson_media')->withPivot('is_primary')->withTimestamps(); }
    public function progress(){ return $this->hasMany(LessonProgress::class); }
    public function captions(){ return $this->hasMany(Caption::class); }
}

