<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'slug'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_skill');
    }
}
