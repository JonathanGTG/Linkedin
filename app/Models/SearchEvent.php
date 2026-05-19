<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchEvent extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'q', 'filters', 'ip_address', 'user_agent', 'created_at'];
    protected $casts = ['filters' => 'array', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}

