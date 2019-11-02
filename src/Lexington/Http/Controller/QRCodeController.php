<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Vector\Http\Request;
use Vector\Http\Response;

final class QRCodeController
{
    public static function index()
    {
        $ticket_id = Request::param('ticket');
        $size      = Request::param('size')   ?? 6;
        $margin    = Request::param('margin') ?? 2;

        \QRCode::png("http://lexington.barringtonschools.org/tickets/{$ticket_id}", false, 'L', $size, $margin);
    }
}
