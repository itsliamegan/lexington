<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class AdminController
{
    public static function index()
    {
        $user = User::findOrFail(Session::get('user_id'));

        Response::view('admin/index', [
            'title'        => 'Admin',
            'current_user' => $user
        ]);
    }
}
