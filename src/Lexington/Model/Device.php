<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;
use Lexington\Http\RequestData\DeviceRequestData;

final class Device extends Model
{
    protected $casts = [
        'is_loaner' => 'boolean'
    ];

    protected $fillable = [
        'id',
        'name',
        'serial_number',
        'asset_tag',
        'is_loaner'
    ];

    public function scopeIndex($query)
    {
        return $query
            ->orderBy('name', 'asc')
            ->get();
    }

    public function scopePage($query, $amount_per_page, $page)
    {
        return $query
            ->orderBy('name', 'asc')
            ->offset($amount_per_page * ($page - 1))
            ->limit($amount_per_page)
            ->get();
    }

    public static function maxPage($amount_per_page)
    {
        return ceil(self::index()->count() / $amount_per_page);
    }

    public function scopeShow($query, $asset_tag)
    {
        return $query
            ->where('asset_tag', $asset_tag)
            ->firstOrFail();
    }

    public function scopeSearch($q, $search_query)
    {
        return $q
            ->where('name', 'like', "%{$search_query}%")
            ->orWhere('asset_tag', 'like', "%{$search_query}%")
            ->orWhere('serial_number', 'like', "%{$search_query}%")
            ->get();
    }

    public function scopeLoaners($query)
    {
        return $query->where('is_loaner', true);
    }

    public static function createFromData(DeviceRequestData $data)
    {
        $device = self::create([
            'name'          => $data->name,
            'serial_number' => $data->serialNumber,
            'asset_tag'     => $data->assetTag,
            'is_loaner'     => $data->isLoaner
        ]);

        return $device;
    }

    public function updateFromData(DeviceRequestData $data)
    {
        $device = $this;

        $device->fill([
            'name'          => $data->name,
            'serial_number' => $data->serialNumber,
            'asset_tag'     => $data->assetTag,
            'is_loaner'     => $data->isLoaner
        ]);

        $device->save();
        $device->refresh();

        return $device;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function loans()
    {
        return $this->hasMany(Ticket::class, 'loaner_id');
    }

    public function getIsAvailableAttribute()
    {
        if (! $this->is_loaner) {
            return false;
        }

        return (
            $this
                ->tickets()
                ->where('status_id', '!=', 13)
                ->get()
                ->isEmpty() &&
            $this
                ->loans()
                ->where('status_id', '!=', 13)
                ->get()
                ->isEmpty()
        );
    }
}
