<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class AllowedEmail extends Model
{
    public $timestamps = false;

    public static function isAllowed($email): bool
    {
        return self::query()
            ->where('email', $email)
            ->get()
            ->isNotEmpty();
    }
}
