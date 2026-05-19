<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaRendition extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'media_asset_id', 'storage_object_id', 'label', 'width', 'height', 'bitrate'];

    public function mediaAsset()    { return $this->belongsTo(MediaAsset::class); }
    public function storageObject() { return $this->belongsTo(StorageObject::class, 'storage_object_id'); }
}

