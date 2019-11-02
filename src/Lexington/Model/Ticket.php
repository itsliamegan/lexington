<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;
use Lexington\Http\RequestData\TicketRequestData;

final class Ticket extends Model
{
    protected $fillable = [
        'id',
        'description',
        'status_id',
        'issue_id',
        'liability_id',
        'school_id',
        'device_id',
        'loaner_id',
        'created_at',
        'updated_at',
    ];

    public function scopeIndex($query)
    {
        return $query
            ->where('status_id', '!=', 13)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function scopeSearch($q, $searchQuery)
    {
        return $q
            ->where('id', $searchQuery)
            ->orWhereHas('status', function ($q) use ($searchQuery) {
                return $q->where('name', 'like', "%$searchQuery%");
            })
            ->orWhereHas('issue', function ($q) use ($searchQuery) {
                return $q->where('name', 'like', "%$searchQuery%");
            })
            ->orWhereHas('liability', function ($q) use ($searchQuery) {
                return $q->where('name', 'like', "%$searchQuery%");
            })
            ->orWhereHas('school', function ($q) use ($searchQuery) {
                return $q->where('name', 'like', "%$searchQuery%");
            })
            ->orWhereHas('device', function ($q) use ($searchQuery) {
                return $q
                    ->where('name', 'like', "%$searchQuery%")
                    ->orWhere('serial_number', 'like', "%$searchQuery%")
                    ->orWhere('asset_tag', 'like', "%$searchQuery%");
            })
            ->orWhereHas('loaner', function ($q) use ($searchQuery) {
                return $q
                    ->where('name', 'like', "%$searchQuery%")
                    ->orWhere('serial_number', 'like', "%$searchQuery%")
                    ->orWhere('asset_tag', 'like', "%$searchQuery%");
            })
            ->get();
    }

    public static function createFromData(TicketRequestData $data): self
    {
        $device = Device::query()->where('name', $data->deviceName)->first();
        $loaner = Device::query()->where('name', $data->loanerName)->first();

        $device_id = is_null($device) ? null : $device->id;
        $loaner_id = is_null($loaner) ? null : $loaner->id;

        $ticket = self::create([
            'description'  => $data->description,
            'status_id'    => $data->statusId,
            'issue_id'     => $data->issueId,
            'liability_id' => $data->liabilityId,
            'school_id'    => $data->schoolId,
            'device_id'    => $device_id,
            'loaner_id'    => $loaner_id
        ]);

        $update_changes = [
            "Created the ticket with the description \"{$ticket->description}\"",
            "Created the ticket with the status {$ticket->status->name}",
            "Created the ticket with the issue {$ticket->issue->name}",
            "Created the ticket with the liability {$ticket->liability->name}",
            "Created the ticket with the school {$ticket->school->name}"
        ];

        if (!is_null($device_id)) {
            $update_changes[] = "Created the ticket with the device {$device->name}";
        }

        if (!is_null($loaner_id)) {
            $update_changes[] = "Created the ticket with the loaner {$loaner->name}";
        }

        TicketUpdate::create([
            'ticket_id'   => $ticket->id,
            'is_creation' => true,
            'changes'     => $update_changes,
            'created_by'  => $data->userId
        ]);

        return $ticket;
    }

    public function updateFromData(TicketRequestData $data): self
    {
        $ticket = $this;
        $device = Device::query()->where('name', $data->deviceName)->first();
        $loaner = Device::query()->where('name', $data->loanerName)->first();

        $device_id = is_null($device) ? null : $device->id;
        $loaner_id = is_null($loaner) ? null : $loaner->id;

        $update_changes = [];

        if ($ticket->description !== $data->description) {
            $update_changes[] = "Changed the description from \"{$ticket->description}\" to \"{$data->description}\"";
        }

        if ($ticket->status_id !== $data->statusId) {
            $old_status = $ticket->status;
            $new_status = Status::query()->find($data->statusId);

            $update_changes[] = "Changed the status from {$old_status->name} to {$new_status->name}";
        }

        if ($ticket->issue_id !== $data->issueId) {
            $old_issue = $ticket->issue;
            $new_issue = Issue::query()->find($data->issueId);

            $update_changes[] = "Changed the issue from {$old_issue->name} to {$new_issue->name}";
        }

        if ($ticket->liability_id !== $data->liabilityId) {
            $old_liability = $ticket->liability;
            $new_liability = Liability::query()->find($data->liabilityId);

            $update_changes[] = "Changed the liability from {$old_liability->name} to {$new_liability->name}";
        }

        if ($ticket->school_id !== $data->schoolId) {
            $old_school = $ticket->school;
            $new_school = School::query()->find($data->schoolId);

            $update_changes[] = "Changed the school from {$old_school->name} to {$new_school->name}";
        }

        // changed the device
        if (!is_null($ticket->device) && !is_null($device_id) && $ticket->device->id !== $device_id) {
            $old_device = $ticket->device;
            $new_device = $device;

            $update_changes[] = "Changed the device from {$old_device->name} to {$new_device->name}";
        }

        // added the device
        if (is_null($ticket->device) && !is_null($device_id)) {
            $new_device = $device;

            $update_changes[] = "Added the device {$new_device->name}";
        }

        // removed the device
        if (!is_null($ticket->device) && is_null($device_id)) {
            $old_device = $ticket->device;

            $update_changes[] = "Removed the device {$old_device->name}";
        }

        // changed the loaner
        if (!is_null($ticket->loaner) && !is_null($loaner_id)  && $ticket->loaner->id !== $loaner_id) {
            $old_loaner = $ticket->loaner;
            $new_loaner = $loaner;

            $update_changes[] = "Changed the loaner from {$old_loaner->name} to {$new_loaner->name}";
        }

        // added the loaner
        if (is_null($ticket->loaner) && !is_null($loaner_id)) {
            $new_loaner = $loaner;

            $update_changes[] = "Added the loaner {$new_loaner->name}";
        }

        // removed the loaner
        if (!is_null($ticket->loaner) && is_null($loaner_id)) {
            $old_loaner = $ticket->loaner;

            $update_changes[] = "Removed the loaner {$old_loaner->name}";
        }

        if (!empty($update_changes)) {
            TicketUpdate::create([
                'ticket_id'   => $ticket->id,
                'is_creation' => false,
                'changes'     => $update_changes,
                'created_by'  => $data->userId
            ]);
        }

        $ticket->fill([
            'description'  => $data->description,
            'status_id'    => $data->statusId,
            'issue_id'     => $data->issueId,
            'liability_id' => $data->liabilityId,
            'school_id'    => $data->schoolId,
            'device_id'    => $device_id,
            'loaner_id'    => $loaner_id
        ]);

        $ticket->save();
        $ticket->refresh();

        return $ticket;
    }

    public function advanceToNextStatus($user_id)
    {
        $ticket      = $this;
        $next_status = $ticket->nextStatus;

        if (!is_null($next_status)) {
            if ($ticket->status_id !== $next_status->id) {
                $old_status = $ticket->status;
                $new_status = $next_status;

                TicketUpdate::create([
                    'ticket_id'   => $ticket->id,
                    'is_creation' => false,
                    'changes'     => [
                        "Changed the status from {$old_status->name} to {$new_status->name}"
                    ],
                    'created_by'  => $user_id
                ]);
            }

            $ticket->status_id = $next_status->id;
        }

        $ticket->save();
        $ticket->refresh();

        return $ticket;
    }

    public function resolve($user_id)
    {
        $ticket = $this;

        if ($ticket->status->id !== 13) {
            TicketUpdate::create([
                'ticket_id'   => $ticket->id,
                'is_creation' => false,
                'changes'     => [
                    "Changed the status from {$ticket->status->name} to Resolved"
                ],
                'created_by'  => $user_id
            ]);

            $ticket->status_id = 13;
            $ticket->save();
        }
    }

    public function getNextStatusAttribute()
    {
        // FIXME: This code is very long, ugly, and fragile

        $status    = $this->status;
        $liability = $this->liability;
        $school    = $this->school;

        if ($status->code === 'bhs_in' || $status->code === 'bms_out') {
            if ($liability->code === 'war') {
                return Status::byCode('war_call');
            }

            if ($liability->code === 'bill') {
                return Status::byCode('bill_repair');
            }

            if ($liability->code === 'non_bill') {
                return Status::byCode('non_bill_repair');
            }

            if ($liability->code === 'ins') {
                return Status::byCode('ins_repair');
            }
        }

        if ($status->code === 'war_call' || $status->code === 'ins_call') {
            return Status::byCode('wait_box');
        }

        if ($status->code === 'wait_box') {
            return Status::byCode('wait_repair');
        }

        if ($status->code === 'wait_repair') {
            if ($school->code === 'bhs') {
                return Status::byCode('bhs_return');
            }

            if ($school->code === 'bms') {
                return Status::byCode('bms_return');
            }

            return Status::byCode('resolved');
        }

        if ($status->code === 'bill_repair') {
            if ($school->code === 'bhs') {
                return Status::byCode('wait_payment');
            }

            if ($school->code === 'bms') {
                return Status::byCode('wait_payment_bms');
            }

            return Status::byCode('resolved');
        }

        if ($status->code === 'wait_payment' || $status->code === 'wait_payment_bms') {
            return Status::byCode('resolved');
        }

        if ($status->code === 'non_bill_repair') {
            if ($school->code === 'bhs') {
                return Status::byCode('bhs_return');
            }

            if ($school->code === 'bms') {
                return Status::byCode('bms_return');
            }

            return Status::byCode('resolved');
        }

        if ($status->code === 'bhs_return' || $status->code === 'bms_return') {
            return Status::byCode('resolved');
        }

        if ($status->code === 'wait_parts') {
            if ($liability->code === 'bill') {
                return Status::byCode('bill_repair');
            }

            if ($liability->code === 'non_bill') {
                return Status::byCode('non_bill_repair');
            }
        }

        return null;
    }

    public function getHasNextStatusAttribute()
    {
        return !is_null($this->nextStatus);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function liability()
    {
        return $this->belongsTo(Liability::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function loaner()
    {
        return $this->belongsTo(Device::class);
    }

    public function updates()
    {
        return $this->hasMany(TicketUpdate::class)->orderBy('created_at', 'DESC');
    }
}
