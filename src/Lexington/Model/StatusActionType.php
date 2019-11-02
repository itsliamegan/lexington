<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class StatusActionType extends Model
{
    public $timestamps = false;

    public function scopeIndex($query)
    {
        return $query
            ->orderBy('name', 'asc')
            ->get();
    }

    public function members()
    {
        return $this->hasMany(Status::class);
    }
}
