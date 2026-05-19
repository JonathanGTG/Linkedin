<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','avatar','headline','role'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    public function enrollments()    { return $this->hasMany(Enrollment::class); }
    public function lessonProgress()  { return $this->hasMany(LessonProgress::class); }
    public function reviews()         { return $this->hasMany(CourseReview::class); }
    public function isAdmin(): bool  { return $this->role === 'admin'; }

    public function inProgressCourses() { return $this->enrollments()->where('status','in_progress'); }
    public function savedCourses()      { return $this->enrollments()->where('status','saved'); }
    public function completedCourses()  { return $this->enrollments()->where('status','completed'); }
}
