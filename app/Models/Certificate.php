<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'user_id', 'course_id', 'enrollment_id', 'template_id', 'issued_at'];
    protected $casts = ['issued_at' => 'datetime'];

    public function user()      { return $this->belongsTo(User::class); }
    public function course()    { return $this->belongsTo(Course::class); }
    public function enrollment(){ return $this->belongsTo(Enrollment::class); }
    public function template()  { return $this->belongsTo(CertificateTemplate::class, 'template_id'); }
}

