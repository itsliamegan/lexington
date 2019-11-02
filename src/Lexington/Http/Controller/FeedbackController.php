<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\Feedback;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class FeedbackController
{
    public static function index()
    {
        $user = User::findOrFail(Session::get('user_id'));

        Response::view('feedback', [
            'title'        => 'Feedback',
            'current_user' => $user
        ]);
    }

    public static function create()
    {
        $body    = Request::body();
        $content = $body['content'];
        $user    = User::findOrFail(Session::get('user_id'));
        
        Feedback::create([
            'content'    => $content,
            'created_by' => $user->id
        ]);

        Response::redirect('/lexington/tickets');
    }
}
