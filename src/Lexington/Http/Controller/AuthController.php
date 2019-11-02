<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\AllowedEmail;
use Lexington\Model\User;
use Vector\Http\Exception\BadRequestException;
use Vector\Http\Exception\ForbiddenException;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class AuthController
{
    public static function signIn()
    {
        if (Session::has('user_id')) {
            Response::redirect('/lexington/tickets');
        }

        Response::view('sign-in', [
            'title' => 'Sign In'
        ]);
    }

    public static function create()
    {
        $client = new \Google_Client([
            'client_id' => '96510064562-868mpinnin5a05ud7alenl3gmt1s6c5l.apps.googleusercontent.com'
        ]);
        $token = Request::body()['token'];

        $payload = $client->verifyIdToken($token);
            
        if (!$payload) {
            throw new BadRequestException('Your Google Sign In token is invalid');
        }

        $google_id = $payload['sub'];
        $email     = $payload['email'];
        $name      = $payload['name'];
        $photo     = $payload['picture'];

        if (!AllowedEmail::isAllowed($email)) {
            throw new ForbiddenException('You do not have access to this site');
        }

        $user = User::updateOrCreate([
                'google_id' => $google_id
            ], [
                'google_id' => $google_id,
                'email'     => $email,
                'name'      => $name,
                'photo'     => $photo
            ]);

        Session::set('user_id', $user->id);
        Response::redirect('/lexington/tickets');
    }

    public static function signOut()
    {
        Session::end();
        Response::redirect('/lexington/sign-in');
    }
}
