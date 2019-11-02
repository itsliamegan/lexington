<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Http\RequestData\TicketRequestData;
use Lexington\Model\Issue;
use Lexington\Model\Liability;
use Lexington\Model\School;
use Lexington\Model\Status;
use Lexington\Model\Ticket;
use Lexington\Model\User;
use Vector\Http\Exception\BadRequestException;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class TicketsController
{
    public static function index()
    {
        $tickets = Ticket::index();
        $user    = User::findOrFail(Session::get('user_id'));

        Response::view('tickets/index', [
            'title'        => 'Open Tickets',
            'current_user' => $user,
            'tickets'      => $tickets
        ]);
    }

    public static function new()
    {
        $statuses    = Status::index();
        $issues      = Issue::index();
        $liabilities = Liability::index();
        $schools     = School::index();
        $user        = User::findOrFail(Session::get('user_id'));

        Response::view('tickets/show', [
            'title'        => 'New Ticket',
            'current_user' => $user,
            'is_creation'  => true,
            'statuses'     => $statuses,
            'issues'       => $issues,
            'liabilities'  => $liabilities,
            'schools'      => $schools
        ]);
    }

    public static function create()
    {
        $user_id = Session::get('user_id');
        $body    = Request::body();
        $data    = new TicketRequestData($body, $user_id);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $ticket  = Ticket::createFromData($data);

        Response::redirect("/lexington/tickets/{$ticket->id}");
    }

    public static function show($id)
    {
        $ticket      = Ticket::findOrFail($id);
        $statuses    = Status::index();
        $issues      = Issue::index();
        $liabilities = Liability::index();
        $schools     = School::index();
        $user        = User::findOrFail(Session::get('user_id'));

        Response::view('tickets/show', [
            'title'        => "#{$ticket->id} : {$ticket->issue->name}",
            'current_user' => $user,
            'ticket'       => $ticket,
            'statuses'     => $statuses,
            'issues'       => $issues,
            'liabilities'  => $liabilities,
            'schools'      => $schools
        ]);
    }

    public static function update($id)
    {
        $ticket  = Ticket::findOrFail($id);
        $user_id = Session::get('user_id');
        $body    = Request::body();
        $data    = new TicketRequestData($body, $user_id);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $ticket->updateFromData($data);

        Response::redirect("/lexington/tickets/{$ticket->id}");
    }

    public static function action()
    {
        $body       = Request::body();
        $action     = $body['action'];
        $ticket_ids = $body['tickets'];
        $user_id    = Session::get('user_id');

        foreach ($ticket_ids as $ticket_id) {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($action === 'next') {
                $ticket->advanceToNextStatus($user_id);
            }

            if ($action === 'resolve') {
                $ticket->resolve($user_id);
            }
        }

        Response::redirect('/lexington/tickets');
    }

    public static function next($id)
    {
        $ticket  = Ticket::findOrFail($id);
        $user_id = Session::get('user_id');
        $ticket->advanceToNextStatus($user_id);

        Response::redirect("/lexington/tickets/{$ticket->id}");
    }

    public static function search()
    {
        $query   = Request::param('q');
        $tickets = Ticket::search($query);
        $user    = User::findOrFail(Session::get('user_id'));

        Response::view('tickets/search', [
            'title'        => "Tickets Matching \"$query\"",
            'current_user' => $user,
            'search_type'  => 'tickets',
            'search_query' => $query,
            'tickets'      => $tickets
        ]);
    }

    public static function print($id)
    {
        $ticket = Ticket::findOrFail($id);

        Response::view('tickets/print', [
            'ticket' => $ticket
        ]);
    }
}
