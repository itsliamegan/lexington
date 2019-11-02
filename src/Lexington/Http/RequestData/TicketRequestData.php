<?php declare(strict_types=1);

namespace Lexington\Http\RequestData;

final class TicketRequestData
{
    /** @var ?string */
    public $description;
    /** @var ?int */
    public $statusId;
    /** @var ?int */
    public $issueId;
    /** @var ?int */
    public $liabilityId;
    /** @var ?int */
    public $schoolId;
    /** @var ?int */
    public $deviceName;
    /** @var ?int */
    public $loanerName;
    /** @var ?int */
    public $userId;

    public function __construct(array $body, ?int $user_id)
    {
        $this->description = $body['description']         ?? null;
        $this->statusId    = intval($body['status'])      ?? null;
        $this->issueId     = intval($body['issue'])       ?? null;
        $this->liabilityId = intval($body['liability'])   ?? null;
        $this->schoolId    = intval($body['school'])      ?? null;
        $this->deviceName  = $body['device']              ?? null;
        $this->loanerName  = $body['loaner']              ?? null;
        $this->userId      = $user_id;
    }

    public function isValid() : bool
    {
        if (
            is_null($this->description) ||
            is_null($this->statusId)    ||
            is_null($this->issueId)     ||
            is_null($this->liabilityId) ||
            is_null($this->schoolId)    ||
            is_null($this->userId)
        ) {
            return false;
        }

        return true;
    }
}
