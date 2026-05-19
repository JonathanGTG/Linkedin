<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id','course_id','chapter_id','title','slug','video_url','durasi','durasi_detik','urutan','is_preview','tipe'];
    protected $casts = ['is_preview' => 'boolean'];

    public function course()    { return $this->belongsTo(Course::class); }
    public function chapter()   { return $this->belongsTo(Chapter::class); }
    public function progresses(){ return $this->hasMany(VideoProgress::class); }

    public function getDurasiFormatAttribute(): string
    {
        return sprintf('%d:%02d', intdiv($this->durasi_detik, 60), $this->durasi_detik % 60);
    }
}
