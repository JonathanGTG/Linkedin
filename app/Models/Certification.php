<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','source_url','description','provider',
        'logo','type','level','durasi','durasi_detik',
        'tanggal_rilis','rating','jumlah_rating','jumlah_learner',
        'skills','konten_path','jumlah_course','is_published',
    ];
    protected $casts = [
        'durasi_detik' => 'integer',
        'rating' => 'decimal:1',
        'jumlah_rating' => 'integer',
        'jumlah_learner' => 'integer',
        'jumlah_course' => 'integer',
        'is_published' => 'boolean',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'professional_certificate' => 'Professional Certificate',
            'certification_prep'       => 'Certification Preparation',
            'practice_exam'            => 'Practice Exam',
            'continuing_education'     => 'Continuing Education (CEU)',
            default                    => $this->type,
        };
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
}
