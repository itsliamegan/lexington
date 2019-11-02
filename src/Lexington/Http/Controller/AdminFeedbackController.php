<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\Feedback;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class AdminFeedbackController
{
    public static function index()
    {
        $feedback = Feedback::all();
        $user     = User::findOrFail(Session::get('user_id'));

        Response::view('admin/feedback/index', [
            'title'        => 'Feedback',
            'current_user' => $user,
            'feedback'     => $feedback
        ]);
    }
}
