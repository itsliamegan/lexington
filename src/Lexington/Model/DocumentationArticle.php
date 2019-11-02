<?php declare(strict_types=1);

namespace Lexington\Model;

use Illuminate\Database\Eloquent\Model;

final class DocumentationArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'created_by',
        'updated_by'
    ];

    public function scopeShow($query, $slug)
    {
        return $query
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
