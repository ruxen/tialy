<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'original_url'
    ];

    public function logs()
    {
        return $this->hasMany(UrlLog::class);
    }

    /**
     * Log a visit for this URL
     *
     */
    public function logVisit(): void
    {
        $this->logs()->create([
            'visitor_ip' => request()->ip(),
            'visitor_agent' => request()->userAgent(),
        ]);
    }

}
