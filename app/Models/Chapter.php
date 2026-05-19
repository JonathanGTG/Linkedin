<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'course_id', 'judul', 'urutan', 'jumlah_video'];

    public function course()  { return $this->belongsTo(Course::class); }
    public function videos()  { return $this->hasMany(Video::class)->orderBy('urutan'); }
}
