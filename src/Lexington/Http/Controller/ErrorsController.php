<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lexington\Model\User;
use Vector\Http\Response;
use Vector\Http\Session;

final class ErrorsController
{
    public static function handle(\Throwable $e)
    {
        $code = 500;

        if ($e->getCode() >= 400 && $e->getCode() < 500) {
            $code = $e->getCode();
        }

        if ($e instanceof ModelNotFoundException) {
            $code = 404;
        }

        switch ($code) {
            case 400:
                $message = $e->getMessage();
                break;

            case 403:
                $message = $e->getMessage();
                break;

            case 404:
                $message = 'Not Found';
                break;

            case 405:
                $message = 'Method Not Allowed';
                break;

            case 500:
            default:
                error_log(strval($e));
                $message = 'Internal Server Error';
                break;
        }

        $user     = User::find(Session::get('user_id'));
        $template = is_null($user) ? 'error' : 'layout-error';

        Response::status($code);
        Response::view($template, [
            'message'      => $message,
            'current_user' => $user
        ]);
    }
}
