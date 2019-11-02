<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Common\Arrays;
use Lexington\Model\Device;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class LoanersController
{
    public static function index()
    {
        $loaners = Device::loaners()->get();
        $user    = User::findOrFail(Session::get('user_id'));

        Response::view('loaners/index', [
            'title'        => 'Loaners',
            'current_user' => $user,
            'search_type'  => 'devices',
            'loaners'      => $loaners
        ]);
    }

    public static function searchJson()
    {
        $query   = Request::param('q');
        $loaners = Device::loaners()->search($query);

        Response::json(Arrays::camelcase($loaners->toArray()));
    }
}
