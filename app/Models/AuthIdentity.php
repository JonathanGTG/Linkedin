<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthIdentity extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','provider','provider_user_id','email',
        'access_token','refresh_token','expires_at',
    ];
    protected $casts = ['expires_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}

