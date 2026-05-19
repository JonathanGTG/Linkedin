<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseFile extends Model
{
    use HasFactory;
    protected $fillable = ['course_id', 'lesson_id', 'storage_object_id', 'title', 'file_type'];

    public function course()       { return $this->belongsTo(Course::class); }
    public function lesson()       { return $this->belongsTo(Lesson::class); }
    public function storageObject(){ return $this->belongsTo(StorageObject::class, 'storage_object_id'); }
}

