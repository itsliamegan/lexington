<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class TicketUpdate extends Model
{
    const UPDATED_AT = null;

    protected $casts = [
        'is_creation' => 'boolean',
        'changes'     => 'array'
    ];

    protected $fillable = [
        'ticket_id',
        'is_creation',
        'changes',
        'created_by'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
