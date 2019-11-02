<?php declare(strict_types=1);

namespace Lexington\Http\RequestData;

final class StatusRequestData
{
    /** @var ?string */
    public $name;
    /** @var ?string */
    public $code;
    /** @var ?int */
    public $placement;
    /** @var ?int */
    public $actionTypeId;

    public function __construct(array $body)
    {
        $this->name         = $body['name']                ?? null;
        $this->code         = $body['code']                ?? null;
        $this->placement    = intval($body['placement'])   ?? null;
        $this->actionTypeId = intval($body['action-type']) ?? null;
    }

    public function isValid() : bool
    {
        if (
            is_null($this->name) ||
            is_null($this->code) ||
            is_null($this->placement) ||
            is_null($this->actionTypeId)
        ) {
            return false;
        }

        return true;
    }
}
