<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'reference',
        'issuer',
        'client',
        'items',
        'totals',
        'legal',
        'footer',
        'user_id',
        'organisation_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'issuer' => 'array',
        'client' => 'array',
        'items' => 'array',
        'totals' => 'array',
        'legal' => 'array',
        'footer' => 'array',
    ];

    protected array $searchable = [
        'invoice_number',
        'client_name',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $query->where(function ($q) use ($term) {
            foreach ($this->searchable as $column) {
                $q->orWhere($column, 'like', "%{$term}%");
            }
        });

        return $query;
    }
}
