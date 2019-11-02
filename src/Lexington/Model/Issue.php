<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class Issue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code'
    ];
    
    public function scopeIndex($query)
    {
        return $query
            ->orderBy('name', 'asc')
            ->get();
    }

    public function scopeShow($query, $code)
    {
        return $query
            ->where('code', $code)
            ->firstOrFail();
    }
}
