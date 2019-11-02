<?php declare(strict_types=1);

namespace Lexington\Http\Middleware;

use Lexington\Model\User;
use Vector\Http\Exception\ForbiddenException;
use Vector\Http\Request;
use Vector\Http\Session;

final class VerifyUserIsAdmin
{
    public function __invoke()
    {
        $request_uri    = Request::uri();
        $is_admin_route = strpos($request_uri, 'admin') !== false;
        $user           = User::find(Session::get('user_id'));

        if ($is_admin_route && ! $user->is_admin) {
            throw new ForbiddenException;
        }
    }
}
