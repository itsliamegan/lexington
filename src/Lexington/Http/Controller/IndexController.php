<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Vector\Http\Response;

final class IndexController
{
    public static function index()
    {
        Response::redirect('/lexington/tickets', 301);
    }
}
