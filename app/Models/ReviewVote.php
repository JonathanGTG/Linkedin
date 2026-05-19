<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewVote extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'review_id', 'is_helpful', 'created_at'];
    protected $casts = ['is_helpful' => 'boolean', 'created_at' => 'datetime'];

    public function user()   { return $this->belongsTo(User::class); }
    public function review() { return $this->belongsTo(CourseReview::class, 'review_id'); }
}

