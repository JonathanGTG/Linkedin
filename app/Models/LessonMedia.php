<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonMedia extends Model
{
    use HasFactory;
    protected $table = 'lesson_media';
    protected $fillable = ['lesson_id', 'media_asset_id', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];

    public function lesson()    { return $this->belongsTo(Lesson::class); }
    public function mediaAsset(){ return $this->belongsTo(MediaAsset::class); }
}

