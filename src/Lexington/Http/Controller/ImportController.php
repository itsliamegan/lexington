<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Common\CSV;
use Lexington\Model\Device;
use Lexington\Model\Ticket;
use Lexington\Model\TicketUpdate;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class ImportController
{
    public static function index()
    {
        $user = User::findOrFail(Session::get('user_id'));

        Response::view('admin/import/index', [
            'title'        => 'Import',
            'current_user' => $user
        ]);
    }

    public static function importTickets()
    {
        if (isset($_FILES['tickets-csv']) && $_FILES['tickets-csv']['tmp_name'] != '') {
            $old_tickets = CSV::rows($_FILES['tickets-csv']['tmp_name']);

            foreach ($old_tickets as $old_ticket) {
                // for some reason "ticket_id" doesn't exist as a key
                // even though it does; this is a terrible hack
                $ticket_id = $old_ticket[array_keys($old_ticket)[0]];

                $old_status_id = $old_ticket['status_id'];
                $new_status_id = self::getStatusId($old_status_id);

                if ($old_ticket['active'] == 1) {
                    $new_status_id = 13;
                }

                $old_issue_id = $old_ticket['issue_id'];
                $new_issue_id = self::getIssueId($old_issue_id);

                $old_liability_id = $old_ticket['payment_option_id'];
                $new_liability_id = self::getLiabilityId($old_liability_id);

                $old_school_id = $old_ticket['school_id'];
                $new_school_id = self::getSchoolId($old_school_id);

                $new_ticket = Ticket::create([
                    'id'           => $ticket_id,
                    'description'  => $old_ticket['description'],
                    'status_id'    => $new_status_id,
                    'issue_id'     => $new_issue_id,
                    'liability_id' => $new_liability_id,
                    'school_id'    => $new_school_id,
                    'created_at'   => $old_ticket['timestamp']
                ]);

                TicketUpdate::create([
                    'ticket_id'   => $new_ticket->id,
                    'is_creation' => true,
                    'changes'     => [
                        'Migrated the ticket from Lexington v1 to Lexington v2'
                    ],
                    'created_by'  => 2
                ]);
            }
        }

        Response::redirect('/lexington/tickets');
    }

    private static function getStatusId($old_status_id)
    {
        $old_to_new_ids = [
            1 => 1,
            2 => 1,
            3 => 11,
            4 => 7,
            5 => 13,
            6 => 2,
            7 => 1,
            8 => 13,
            9 => 6,
            10 => 3,
            11 => 13,
            12 => 5,
            13 => 6,
            14 => 4,
            15 => 13,
            16 => 5,
            17 => 16,
            19 => 17,
            20 => 18,
            21 => 13,
            22 => 10,
            23 => 13,
            24 => 13,
            25 => 8,
            26 => 9,
            27 => 13,
            28 => 12,
            29 => 15,
            31 => 13,
        ];

        $new_id = $old_to_new_ids[$old_status_id] ?? 15;

        return $new_id;
    }

    private static function getIssueId($old_issue_id)
    {
        $old_to_new_ids = [
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 6,
            5 => 5,
            6 => 7,
            7 => 1,
            8 => 11,
            9 => 10,
            10 => 9,
            11 => 13,
            12 => 14,
            13 => 12,
        ];

        return $old_to_new_ids[$old_issue_id] ?? 14;
    }

    private static function getLiabilityId($old_liability_id)
    {
        $old_to_new_ids = [
            1 => 4,
            2 => 2,
            3 => 3,
            4 => 1,
            5 => 5,
        ];

        return $old_to_new_ids[$old_liability_id] ?? 5;
    }

    private static function getSchoolId($old_school_id)
    {
        $old_to_new_ids = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            9 => 7,
        ];

        return $old_to_new_ids[$old_school_id] ?? 1;
    }

    public static function importDevicesFromLexington()
    {
        if (
            isset($_FILES['devices-csv']) && 
            $_FILES['devices-csv']['tmp_name'] != '' &&
            isset($_FILES['associations-csv']) &&
            $_FILES['associations-csv']['tmp_name'] != ''
        ) {
            $old_devices      = CSV::rows($_FILES['devices-csv']['tmp_name']);
            $old_associations = CSV::rows($_FILES['associations-csv']['tmp_name']);

            foreach ($old_devices as $old_device) {
                $device_id            = $old_device[array_keys($old_device)[0]];
                $device_name          = $old_device['name'];
                $device_serial_number = $old_device['serial_number'];
                $device_asset_tag     = $old_device['asset'];
                $is_loaner            = $old_device['device_role_id'] == 2;

                if (
                    !empty($device_name) && 
                    !empty($device_serial_number) &&
                    !empty($device_asset_tag)
                ) {
                    Device::updateOrCreate([
                        'id' => $device_id
                    ], [
                        'id'            => $device_id,
                        'name'          => $device_name,
                        'serial_number' => $device_serial_number,
                        'asset_tag'     => $device_asset_tag,
                        'is_loaner'     => $is_loaner
                    ]);
                }
            }

            foreach ($old_associations as $old_association) {
                $ticket = Ticket::find($old_association['ticket_id']);

                if (!is_null($ticket)) {
                    $device_id = $old_association['device_id'];
                    $loaner_id = $old_association['loaner_id'];

                    if ($device_id != 0) {
                        $ticket->device_id = $device_id;
                    }

                    if ($loaner_id != 0) {
                        $ticket->loaner_id = $loaner_id;
                    }
                }

                $ticket->save();
            }
        }

        Response::redirect('/lexington/devices');
    }

    public static function importDevicesFromGoogle()
    {
    }
}
