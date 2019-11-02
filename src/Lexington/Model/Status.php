<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;
use Lexington\Http\RequestData\StatusRequestData;

final class Status extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'placement',
        'action_type_id'
    ];

    public function scopeIndex($query)
    {
        return $query
            ->orderBy('placement', 'asc')
            ->get();
    }

    public function scopeShow($query, $code)
    {
        return $query
            ->where('code', $code)
            ->firstOrFail();
    }

    public function scopeByCode($query, $code)
    {
        return $query
            ->where('code', $code)
            ->first();
    }

    public static function createFromData(StatusRequestData $data) : self
    {
        $status = self::create([
            'name' => $data->name,
            'code' => $data->code,
            'placement' => $data->placement,
            'action_type_id' => $data->actionTypeId
        ]);

        return $status;
    }

    public function updateFromData(StatusRequestData $data) : self
    {
        $status = $this;

        $status->fill([
            'name' => $data->name,
            'code' => $data->code,
            'placement' => $data->placement,
            'action_type_id' => $data->actionTypeId
        ]);
        
        $status->save();
        $status->refresh();

        return $status;
    }

    public function actionType()
    {
        return $this->belongsTo(StatusActionType::class);
    }
}
