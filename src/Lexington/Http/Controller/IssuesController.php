<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\Issue;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class IssuesController
{
    public static function index()
    {
        $issues = Issue::index();
        $user   = User::findOrFail(Session::get('user_id'));

        Response::view('admin/issues/index', [
            'title'        => 'Issues',
            'current_user' => $user,
            'issues'       => $issues
        ]);
    }

    public static function new()
    {
        $user = User::findOrFail(Session::get('user_id'));

        Response::view('admin/issues/show', [
            'title'        => 'New Issue',
            'current_user' => $user,
            'is_creation'  => true
        ]);
    }

    public static function create()
    {
        $body = Request::body();

        $issue = Issue::create([
            'name' => $body['name']
        ]);

        Response::redirect("/lexington/admin/issues/{$issue->code}");
    }

    public static function show($code)
    {
        $issue = Issue::show($code);
        $user  = User::findOrFail(Session::get('user_id'));

        Response::view('admin/issues/show', [
            'title'        => $issue->name,
            'current_user' => $user,
            'is_creation'  => false,
            'issue'        => $issue
        ]);
    }

    public static function update($code)
    {
        $issue = Issue::show($code);
        $body  = Request::body();

        $issue->fill([
            'name' => $body['name'],
            'code' => $body['code']
        ]);
        $issue->save();
        
        Response::redirect("/lexington/admin/issues/{$issue->code}");
    }
}
