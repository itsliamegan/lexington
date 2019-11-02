<?php declare(strict_types=1);

namespace Lexington\Http\RequestData;

final class DeviceRequestData
{
    /** @var ?string */
    public $name;
    /** @var ?string */
    public $serialNumber;
    /** @var ?string */
    public $assetTag;
    /** @var bool */
    public $isLoaner;

    public function __construct(array $body)
    {
        $this->name         = $body['name']          ?? null;
        $this->serialNumber = $body['serial-number'] ?? null;
        $this->assetTag     = $body['asset-tag']     ?? null;
        $this->isLoaner     = isset($body['is-loaner']);
    }

    public function isValid() : bool
    {
        if (
            is_null($this->name) ||
            is_null($this->serialNumber) ||
            is_null($this->assetTag)
        ) {
            return false;
        }
        
        return true;
    }
}
