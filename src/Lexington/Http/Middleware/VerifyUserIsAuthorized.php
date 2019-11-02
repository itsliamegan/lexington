<?php declare(strict_types=1);

namespace Lexington\Http\Middleware;

use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class VerifyUserIsAuthorized
{
    public function __invoke()
    {
        Session::set('user_id', 1);
        $user_is_authenticated = Session::has('user_id');
        $request_uri           = Request::uri();
        $is_auth_route         = (
            strpos($request_uri, 'sign-in') !== false ||
            strpos($request_uri, 'sign-out') !== false
        );

        if (!$is_auth_route && !$user_is_authenticated) {
            Response::redirect('/lexington/sign-in');
            exit();
        }
    }
}
