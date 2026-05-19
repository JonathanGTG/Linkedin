<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'storage_object_id', 'kind', 'width', 'height', 'durasi_detik'];

    public function storageObject() { return $this->belongsTo(StorageObject::class, 'storage_object_id'); }
    public function renditions()    { return $this->hasMany(MediaRendition::class); }
    public function lessons()       { return $this->belongsToMany(Lesson::class, 'lesson_media')->withPivot('is_primary')->withTimestamps(); }
}

