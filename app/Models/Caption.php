<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caption extends Model
{
    use HasFactory;
    protected $fillable = ['lesson_id', 'storage_object_id', 'language', 'format'];

    public function lesson()        { return $this->belongsTo(Lesson::class); }
    public function storageObject() { return $this->belongsTo(StorageObject::class, 'storage_object_id'); }
}

