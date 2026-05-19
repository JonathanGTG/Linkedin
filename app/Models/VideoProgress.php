<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;
    protected $table = 'video_progress';
    protected $fillable = ['user_id','video_id','detik_terakhir','is_completed'];
    protected $casts = ['is_completed' => 'boolean'];

    public function user()  { return $this->belongsTo(User::class); }
    public function video() { return $this->belongsTo(Video::class); }
}
