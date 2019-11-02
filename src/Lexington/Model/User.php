<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean'
    ];

    protected $fillable = [
        'google_id',
        'name',
        'email',
        'photo'
    ];
}
