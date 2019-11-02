<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Http\RequestData\StatusRequestData;
use Lexington\Model\Status;
use Lexington\Model\StatusActionType;
use Lexington\Model\User;
use Vector\Http\Exception\BadRequestException;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class StatusesController
{
    public static function index()
    {
        $statuses = Status::index();
        $user     = User::findOrFail(Session::get('user_id'));

        Response::view('admin/statuses/index', [
            'title'        => 'Statuses',
            'current_user' => $user,
            'statuses'     => $statuses
        ]);
    }

    public static function new()
    {
        $action_types = StatusActionType::index();
        $user         = User::findOrFail(Session::get('user_id'));

        Response::view('admin/statuses/show', [
            'title'        => 'New Status',
            'current_user' => $user,
            'is_creation'  => true,
            'action_types' => $action_types
        ]);
    }

    public static function create()
    {
        $body   = Request::body();
        $data   = new StatusRequestData($body);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $status = Status::createFromData($data);

        Response::redirect("/lexington/admin/statuses/{$status->code}");
    }

    public static function show($code)
    {
        $status       = Status::show($code);
        $action_types = StatusActionType::index();
        $user         = User::findOrFail(Session::get('user_id'));

        Response::view('admin/statuses/show', [
            'title'        => $status->name,
            'current_user' => $user,
            'is_creation'  => false,
            'status'       => $status,
            'action_types' => $action_types
        ]);
    }

    public static function update($code)
    {
        $status = Status::show($code);
        $body   = Request::body();
        $data   = new StatusRequestData($body);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $status->updateFromData($data);

        Response::redirect("/lexington/admin/statuses/{$status->code}");
    }
}
