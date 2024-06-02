<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UrlLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_id',
        'visitor_ip',
        'visitor_agent'
    ];


    public function url()
    {
        return $this->belongsTo(Url::class);
    }
}
