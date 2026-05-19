<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'course_id', 'lesson_id', 'detik', 'label', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function user()  { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }
}

